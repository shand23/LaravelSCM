<?php

namespace App\Livewire\Logistik\PermintaanProyek;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PermintaanProyek;
use App\Models\StokBatchFifo;
use App\Models\PengajuanPembelian; 
use App\Models\DetailPengajuanPembelian; 
use App\Models\PengeluaranStokFifo; // Import Model Baru
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
    public $riwayatBatch = []; 

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

   public function lihatDetail($id_permintaan)
{
    $this->permintaanTerpilih = PermintaanProyek::with(['proyek', 'user', 'detailPermintaan.material'])->find($id_permintaan);
    
    if ($this->permintaanTerpilih) {
        $this->detailBarang = $this->permintaanTerpilih->detailPermintaan;
        
        // 1. Ambil data mentah dari database
        $riwayatRaw = PengeluaranStokFifo::with('stokBatch')
            ->where('id_permintaan', $id_permintaan)
            ->get();
            
        // 2. Ubah menjadi Array PHP biasa agar Livewire tidak error
        $riwayatArray = [];
        foreach ($riwayatRaw as $rw) {
            $riwayatArray[$rw->id_material][] = [
                'id_stok'        => $rw->id_stok,
                'jumlah_diambil' => $rw->jumlah_diambil,
                'tanggal_masuk'  => $rw->stokBatch ? $rw->stokBatch->tanggal_masuk : null,
            ];
        }
        
        // 3. Simpan array yang sudah rapi ke properti publik
        $this->riwayatBatch = $riwayatArray;

        $this->isModalOpen = true;
    }
}

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->permintaanTerpilih = null;
        $this->detailBarang = [];
        $this->riwayatBatch = [];
    }

    public function prosesPemenuhanStok()
    {
        if (!$this->permintaanTerpilih) return;

        $id_permintaan = $this->permintaanTerpilih->id_permintaan;

        DB::transaction(function () use ($id_permintaan) {
            $permintaan = PermintaanProyek::with('detailPermintaan')->lockForUpdate()->find($id_permintaan);
            
            $semuaTerpenuhi = true; 
            $kekuranganMaterial = []; 

            foreach ($permintaan->detailPermintaan as $detail) {
                $kebutuhanQty = $detail->jumlah_diminta - $detail->jumlah_terkirim;

                if ($kebutuhanQty > 0) {
                    $batches = StokBatchFifo::where('id_material', $detail->id_material)
                                ->where('sisa_stok', '>', 0)
                                ->orderBy('tanggal_masuk', 'asc')
                                ->lockForUpdate() 
                                ->get();

                    $totalDiambilDariGudang = 0;

                    foreach ($batches as $batch) {
                        if ($kebutuhanQty == 0) break; 

                        $jumlahDiambil = 0;

                        if ($batch->sisa_stok >= $kebutuhanQty) {
                            $jumlahDiambil = $kebutuhanQty;
                            $batch->sisa_stok -= $kebutuhanQty;
                            $kebutuhanQty = 0; 
                        } else {
                            $jumlahDiambil = $batch->sisa_stok;
                            $kebutuhanQty -= $batch->sisa_stok;
                            $batch->sisa_stok = 0;
                        }

                        $batch->save();
                        $totalDiambilDariGudang += $jumlahDiambil;

                        // PENCATATAN MENGGUNAKAN ELOQUENT: Log batch mana yang diambil
                        if ($jumlahDiambil > 0) {
                            PengeluaranStokFifo::create([
                                'id_permintaan'  => $id_permintaan,
                                'id_material'    => $detail->id_material,
                                'id_stok'        => $batch->id_stok, // Sesuai dengan kolom DB
                                'jumlah_diambil' => $jumlahDiambil,
                            ]);
                        }
                    }

                    if ($totalDiambilDariGudang > 0) {
                        $detail->jumlah_terkirim += $totalDiambilDariGudang;
                        $detail->save();
                    }

                    if ($kebutuhanQty > 0) {
                        $semuaTerpenuhi = false;
                        $kekuranganMaterial[] = [
                            'id_material' => $detail->id_material,
                            'jumlah_kurang' => $kebutuhanQty
                        ];
                    }
                }
            }

            if ($semuaTerpenuhi) {
                $permintaan->status_permintaan = 'Selesai';
                session()->flash('message', 'Stok mencukupi! Seluruh permintaan berhasil dipenuhi dari gudang.');
            } else {
                $permintaan->status_permintaan = 'Diproses Sebagian';
                
                if (count($kekuranganMaterial) > 0) {
                    $pengajuanBaru = PengajuanPembelian::create([
                        'id_user_logistik' => Auth::id() ?? 'USR001', 
                        'referensi_id_permintaan' => $permintaan->id_permintaan,
                        'tanggal_pengajuan' => date('Y-m-d'),
                        'status_pengajuan' => 'Menunggu Pengadaan',
                    ]);

                    foreach ($kekuranganMaterial as $kurang) {
                        DetailPengajuanPembelian::create([
                            'id_pengajuan' => $pengajuanBaru->id_pengajuan, 
                            'id_material' => $kurang['id_material'],
                            'jumlah_minta_beli' => $kurang['jumlah_kurang'], 
                        ]);
                    }

                    session()->flash('message', 'Diproses Sebagian! Kekurangan material otomatis diteruskan menjadi PR (' . $pengajuanBaru->id_pengajuan . ').');
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
            ->whereIn('status_permintaan', ['Disetujui PM', 'Diproses Sebagian', 'Selesai'])
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