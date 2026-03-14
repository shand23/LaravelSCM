<?php

namespace App\Livewire\Logistik\PermintaanProyek;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PermintaanProyek;
use App\Models\StokBatchFifo;
use App\Models\PengajuanPembelian; 
use App\Models\DetailPengajuanPembelian; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PermintaanProyekLogistikIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    // Variabel Modal Detail
    public $isModalOpen = false;
    public $permintaanTerpilih = null;
    public $detailBarang = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    // Membuka modal dan memuat detail permintaan
    public function lihatDetail($id_permintaan)
    {
        $this->permintaanTerpilih = PermintaanProyek::with(['proyek', 'user', 'detailPermintaan.material'])->find($id_permintaan);
        
        if ($this->permintaanTerpilih) {
            $this->detailBarang = $this->permintaanTerpilih->detailPermintaan;
            $this->isModalOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->permintaanTerpilih = null;
        $this->detailBarang = [];
    }

    // FUNGSI INTI: Eksekusi pemotongan stok FIFO & Auto Create PR
    public function prosesPemenuhanStok()
    {
        if (!$this->permintaanTerpilih) return;

        $id_permintaan = $this->permintaanTerpilih->id_permintaan;

        DB::transaction(function () use ($id_permintaan) {
            // Lock tabel untuk mencegah race condition (double entry)
            $permintaan = PermintaanProyek::with('detailPermintaan')->lockForUpdate()->find($id_permintaan);
            
            $semuaTerpenuhi = true; 
            $kekuranganMaterial = []; // Array penampung material yang harus dibeli (PR)

            foreach ($permintaan->detailPermintaan as $detail) {
                // 1. Hitung sisa kebutuhan yang belum terkirim
                $kebutuhanQty = $detail->jumlah_diminta - $detail->jumlah_terkirim;

                if ($kebutuhanQty > 0) {
                    // 2. Cari stok FIFO di gudang (berdasarkan tanggal masuk paling lama)
                    $batches = StokBatchFifo::where('id_material', $detail->id_material)
                                ->where('sisa_stok', '>', 0)
                                ->orderBy('tanggal_masuk', 'asc')
                                ->lockForUpdate() 
                                ->get();

                    $totalDiambilDariGudang = 0;

                    // 3. Eksekusi pemotongan Batch per Batch
                    foreach ($batches as $batch) {
                        if ($kebutuhanQty == 0) break; 

                        if ($batch->sisa_stok >= $kebutuhanQty) {
                            // Stok di batch ini cukup untuk menutupi semua sisa kebutuhan
                            $batch->sisa_stok -= $kebutuhanQty;
                            $totalDiambilDariGudang += $kebutuhanQty;
                            $kebutuhanQty = 0; 
                            $batch->save();
                        } else {
                            // Stok di batch ini tidak cukup, kuras habis batch ini
                            $kebutuhanQty -= $batch->sisa_stok;
                            $totalDiambilDariGudang += $batch->sisa_stok;
                            $batch->sisa_stok = 0;
                            $batch->save();
                        }
                    }

                    // 4. Update riwayat jumlah terkirim di detail_permintaan_proyek
                    if ($totalDiambilDariGudang > 0) {
                        $detail->jumlah_terkirim += $totalDiambilDariGudang;
                        $detail->save();
                    }

                    // 5. Cek apakah material ini MASIH KURANG setelah gudang dikuras
                    if ($kebutuhanQty > 0) {
                        $semuaTerpenuhi = false;
                        
                        // Masukkan ke daftar belanjaan (PR)
                        $kekuranganMaterial[] = [
                            'id_material' => $detail->id_material,
                            'jumlah_kurang' => $kebutuhanQty
                        ];
                    }
                }
            }

            // 6. Tentukan Status Permintaan & Buat PR otomatis jika ada kurang
            if ($semuaTerpenuhi) {
                $permintaan->status_permintaan = 'Selesai';
                session()->flash('message', 'Stok mencukupi! Seluruh permintaan berhasil dipenuhi dari gudang.');
            } else {
                $permintaan->status_permintaan = 'Diproses Sebagian';
                
                // === LOGIKA AUTO CREATE PENGAJUAN PEMBELIAN (PR) ===
                if (count($kekuranganMaterial) > 0) {
                    
                    // Insert Header PR
                    // Pastikan Auth::id() sesuai dengan format VARCHAR(20) di tabel users Anda.
                    // Jika saat testing tidak ada session login, ini akan pakai ID default 'USR001'
                    $pengajuanBaru = PengajuanPembelian::create([
                        'id_user_logistik' => Auth::id() ?? 'USR001', 
                        'referensi_id_permintaan' => $permintaan->id_permintaan,
                        'tanggal_pengajuan' => date('Y-m-d'),
                        'status_pengajuan' => 'Menunggu Pengadaan', // Menggunakan ENUM dari DB terbaru
                    ]);

                    // Insert Detail PR
                    foreach ($kekuranganMaterial as $kurang) {
                        DetailPengajuanPembelian::create([
                            'id_pengajuan' => $pengajuanBaru->id_pengajuan, 
                            'id_material' => $kurang['id_material'],
                            'jumlah_minta_beli' => $kurang['jumlah_kurang'], // Menggunakan kolom jumlah_minta_beli
                        ]);
                    }

                    session()->flash('message', 'Diproses Sebagian! Kekurangan material otomatis diteruskan menjadi Pengajuan Pembelian (' . $pengajuanBaru->id_pengajuan . ').');
                } else {
                    session()->flash('message', 'Pemotongan FIFO berhasil dijalankan (Diproses Sebagian).');
                }
            }
            
            $permintaan->save();
        });

        $this->closeModal();
    }

    public function render()
    {
        $query = PermintaanProyek::with(['proyek', 'user'])
            ->where(function($q) {
                $q->where('id_permintaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', function($q2) {
                      $q2->where('nama_proyek', 'like', '%' . $this->search . '%');
                  });
            });

        if ($this->filterStatus !== '') {
            $query->where('status_permintaan', $this->filterStatus);
        }

        return view('livewire.logistik.permintaan-proyek.permintaan-proyek-logistik-index', [
            'listPermintaan' => $query->latest('tanggal_permintaan')->paginate(10)
        ]);
    }
}