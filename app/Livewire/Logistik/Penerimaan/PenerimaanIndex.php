<?php

namespace App\Livewire\Logistik\Penerimaan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\PenerimaanMaterial;
use App\Models\DetailPenerimaan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PenerimaanIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isModalOpen = false;
    
    // -- STATE BARU UNTUK MODAL CEK RIWAYAT RETUR --
    public $isModalRiwayatOpen = false;
    public $dataRiwayatRetur = [];
    public $infoRiwayatPenerimaan = null;
    
    // Data Master
    public $listRak = [];
    public $listPengirimanDO = [];

    // Form Header
    public $id_pengiriman;
    public $tanggal_terima;
    public $status_penerimaan = 'Diterima Penuh';
    public $foto_bukti_rusak; 
    
    // Array Detail Pengecekan
    public $detailTerima = [];

    // app/Livewire/Logistik/Penerimaan/PenerimaanIndex.php

public function mount()
{
    $this->tanggal_terima = date('Y-m-d');
    $this->listRak = DB::table('master_lokasi_rak')->get();

    // LOGIKA BARU: Tangkap parameter dari Notifikasi
    $idFromNotif = request()->query('id_pengiriman');
    
    if ($idFromNotif) {
        // 1. Set ID Pengiriman
        $this->id_pengiriman = $idFromNotif;
        
        // 2. Buka Modal Otomatis
        $this->isModalOpen = true;
        
        // 3. Panggil fungsi untuk memuat detail material (fungsi ini sudah ada di file Anda)
        $this->updatedIdPengiriman($idFromNotif);
    }
}

    // Fungsi Tarik Data & Set Default
    public function updatedIdPengiriman($value)
    {
        $this->detailTerima = [];
        $this->status_penerimaan = 'Diterima Penuh'; 

        if ($value) {
            $do = Pengiriman::with(['detailPengiriman.detailKontrak.material.kategori'])->find($value);
            
            if ($do && $do->detailPengiriman) {
                foreach ($do->detailPengiriman as $det) {
                    $this->detailTerima[$det->id_pengiriman_detail] = [
                        'id_detail_kontrak' => $det->id_detail_kontrak,
                        'id_material'       => $det->detailKontrak->id_material,
                        'nama_material'     => $det->detailKontrak->material->nama_material ?? 'Unknown',
                        'nama_kategori'     => $det->detailKontrak->material->kategori->nama_kategori ?? 'Umum',
                        'qty_dikirim'       => $det->jumlah_dikirim,
                        'jumlah_bagus'      => $det->jumlah_dikirim, 
                        'jumlah_rusak'      => 0,                    
                        'alasan_return'     => '',
                        'id_lokasi'         => '',
                    ];
                }
            }
        }
    }

    // Fungsi Auto-Balance & Anti-Minus 
    public function updated($property, $value)
    {
        if (preg_match('/detailTerima\.(.*)\.jumlah_rusak/', $property, $matches)) {
            $idDet = $matches[1];
            $rusak = max(0, (int)$value); 
            $qtyKirim = (int)$this->detailTerima[$idDet]['qty_dikirim'];
            
            if ($rusak > $qtyKirim) $rusak = $qtyKirim;
            
            $this->detailTerima[$idDet]['jumlah_rusak'] = $rusak;
            $this->detailTerima[$idDet]['jumlah_bagus'] = $qtyKirim - $rusak;
            $this->cekStatusOtomatis();
        }
        
        if (preg_match('/detailTerima\.(.*)\.jumlah_bagus/', $property, $matches)) {
            $idDet = $matches[1];
            $bagus = max(0, (int)$value); 
            $qtyKirim = (int)$this->detailTerima[$idDet]['qty_dikirim'];
            
            if ($bagus > $qtyKirim) $bagus = $qtyKirim;
            
            $this->detailTerima[$idDet]['jumlah_bagus'] = $bagus;
            $this->detailTerima[$idDet]['jumlah_rusak'] = $qtyKirim - $bagus;
            $this->cekStatusOtomatis();
        }
    }

    private function cekStatusOtomatis()
    {
        $totalRusak = 0;
        foreach ($this->detailTerima as $item) {
            $totalRusak += (int)$item['jumlah_rusak'];
        }
        $this->status_penerimaan = ($totalRusak > 0) ? 'Diterima Sebagian' : 'Diterima Penuh';
    }

    public function create()
    {
        $this->resetForm();
        
        // Mengambil pengiriman yang statusnya Dalam Perjalanan ATAU Tiba di Lokasi (atau Pending)
        $this->listPengirimanDO = Pengiriman::whereIn('status_pengiriman', ['Dalam Perjalanan', 'Tiba di Lokasi'])->get();
        
        $this->isModalOpen = true;
    }

    // --- FUNGSI BARU UNTUK CEK RIWAYAT RETUR ---
    public function cekRiwayat($id_penerimaan)
    {
        $this->infoRiwayatPenerimaan = $id_penerimaan;
        
        // Tarik detail barang yang jumlah rusaknya lebih dari 0 pada penerimaan ini
        $this->dataRiwayatRetur = DB::table('detail_penerimaan')
            ->join('detail_kontrak', 'detail_penerimaan.id_detail_kontrak', '=', 'detail_kontrak.id_detail_kontrak')
            ->join('material', 'detail_kontrak.id_material', '=', 'material.id_material')
            ->where('detail_penerimaan.id_penerimaan', $id_penerimaan)
            ->where('detail_penerimaan.jumlah_rusak', '>', 0)
            ->select('material.nama_material', 'detail_penerimaan.jumlah_rusak', 'detail_penerimaan.alasan_return', 'detail_penerimaan.foto_bukti_rusak')
            ->get();
            
        $this->isModalRiwayatOpen = true;
    }

    public function closeRiwayatModal()
    {
        $this->isModalRiwayatOpen = false;
        $this->dataRiwayatRetur = [];
        $this->infoRiwayatPenerimaan = null;
    }
    // -------------------------------------------

    public function store()
    {
        $this->validate([
            'id_pengiriman' => 'required',
            'tanggal_terima' => 'required|date',
            'foto_bukti_rusak' => $this->status_penerimaan !== 'Diterima Penuh' ? 'required|image|max:2048' : 'nullable',
        ], [
            'foto_bukti_rusak.required' => 'Bukti foto wajib diupload karena ada laporan barang rusak/retur.'
        ]);

        foreach ($this->detailTerima as $key => $item) {
            $totalInput = (int)$item['jumlah_bagus'] + (int)$item['jumlah_rusak'];
            $qtyKirim = (int)$item['qty_dikirim'];

            if ($totalInput > $qtyKirim) {
                $this->addError("detailTerima.{$key}.jumlah_bagus", "Melebihi DO");
                session()->flash('error', "Gagal! Total fisik {$item['nama_material']} melebihi jumlah DO.");
                return;
            }

            if ((int)$item['jumlah_bagus'] > 0 && empty($item['id_lokasi'])) {
                $this->addError("detailTerima.{$key}.id_lokasi", "Pilih Rak!");
                session()->flash('error', "Gagal! Lokasi Rak wajib dipilih untuk material {$item['nama_material']}.");
                return;
            }

            if ((int)$item['jumlah_rusak'] > 0 && empty($item['alasan_return'])) {
                $this->addError("detailTerima.{$key}.alasan_return", "Alasan wajib diisi!");
                session()->flash('error', "Gagal! Alasan/Catatan wajib diisi karena ada barang rusak pada {$item['nama_material']}.");
                return;
            }
        }

        DB::transaction(function () {
            $pathFoto = $this->foto_bukti_rusak ? $this->foto_bukti_rusak->store('bukti-retur', 'public') : null;

            $penerimaan = PenerimaanMaterial::create([
                'id_pengiriman'     => $this->id_pengiriman,
                'id_user_penerima'  => Auth::id() ?? 'USR001', 
                'tanggal_terima'    => $this->tanggal_terima,
                'status_penerimaan' => $this->status_penerimaan,
            ]);

            foreach ($this->detailTerima as $idDetDO => $item) {
                $newIdDetailTerima = 'DTR' . date('ymdHis') . rand(100, 999);

                DetailPenerimaan::create([
                    'id_detail_terima'     => $newIdDetailTerima,
                    'id_penerimaan'        => $penerimaan->id_penerimaan, 
                    'id_pengiriman_detail' => $idDetDO,
                    'id_detail_kontrak'    => $item['id_detail_kontrak'],
                    'jumlah_bagus'         => $item['jumlah_bagus'],
                    'jumlah_rusak'         => $item['jumlah_rusak'],
                    'alasan_return'        => $item['alasan_return'],
                    'foto_bukti_rusak'     => $pathFoto, 
                ]);

                if ((int)$item['jumlah_bagus'] > 0) {
                    $newIdStok = 'STK' . date('ymdHis') . rand(10, 99);
                    DB::table('stok_batch_fifo')->insert([
                        'id_stok'       => $newIdStok,
                        'id_material'   => $item['id_material'],
                        'id_penerimaan' => $penerimaan->id_penerimaan, 
                        'tanggal_masuk' => $this->tanggal_terima,
                        'jumlah_awal'   => (int)$item['jumlah_bagus'],
                        'sisa_stok'     => (int)$item['jumlah_bagus'],
                        'id_lokasi'     => $item['id_lokasi'], 
                        'status_stok'   => 'Tersedia',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    DB::table('detail_kontrak')
                        ->where('id_detail_kontrak', $item['id_detail_kontrak'])
                        ->increment('jumlah_diterima', (int)$item['jumlah_bagus']);
                }
            }

            $statusPengirimanUpdate = ($this->status_penerimaan == 'Diterima Penuh') ? 'Selesai' : 'Return & Kirim Ulang';
            Pengiriman::where('id_pengiriman', $this->id_pengiriman)->update([
                'status_pengiriman' => $statusPengirimanUpdate
            ]);
        });

        session()->flash('message', 'Penerimaan berhasil! Stok otomatis ditambah & Progres Kontrak diupdate.');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['id_pengiriman', 'detailTerima', 'foto_bukti_rusak']);
        $this->tanggal_terima = date('Y-m-d');
        $this->status_penerimaan = 'Diterima Penuh';
    }

    public function render()
    {
        return view('livewire.logistik.penerimaan.penerimaan-index', [
            'listPenerimaan' => PenerimaanMaterial::with(['pengiriman', 'user']) 
                ->where('id_penerimaan', 'like', "%{$this->search}%")
                ->orWhereHas('pengiriman', function($q) {
                    $q->where('id_pengiriman', 'like', "%{$this->search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}