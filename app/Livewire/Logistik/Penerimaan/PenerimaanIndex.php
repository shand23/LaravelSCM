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

    public function mount()
    {
        $this->tanggal_terima = date('Y-m-d');
        // Ambil master rak dengan kolom Area
        $this->listRak = DB::table('master_lokasi_rak')->get();
    }

    public function updatedIdPengiriman($value)
    {
        $this->detailTerima = [];

        if ($value) {
            $do = Pengiriman::with(['detailPengiriman.detailKontrak.material.kategori'])->find($value);
            
            if ($do && $do->detailPengiriman) {
                foreach ($do->detailPengiriman as $det) {
                    $material = $det->detailKontrak->material;
                    $this->detailTerima[$det->id_pengiriman_detail] = [
                        'id_detail_kontrak' => $det->id_detail_kontrak,
                        'id_material'   => $material->id_material ?? null,
                        'nama_material' => $material->nama_material ?? 'Unknown',
                        'kategori'      => $material->kategori->nama_kategori ?? 'Umum', 
                        'qty_dikirim'   => $det->jumlah_dikirim,
                        'jumlah_bagus'  => $det->jumlah_dikirim, 
                        'jumlah_rusak'  => 0,
                        'id_lokasi'     => '', 
                        'alasan_return' => ''
                    ];
                }
            }
        }
    }

    public function updatedDetailTerima()
    {
        $adaRusak = false;
        foreach ($this->detailTerima as $item) {
            if ((int)($item['jumlah_rusak'] ?? 0) > 0) {
                $adaRusak = true;
                break;
            }
        }
        $this->status_penerimaan = $adaRusak ? 'Diterima Sebagian' : 'Diterima Penuh';
    }

    public function create()
    {
        $this->resetForm();
        $this->listPengirimanDO = Pengiriman::whereIn('status_pengiriman', ['Dalam Perjalanan', 'Tiba di Lokasi'])->get();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'id_pengiriman' => 'required',
            'tanggal_terima' => 'required|date',
            'detailTerima.*.id_lokasi' => 'required_if:detailTerima.*.jumlah_bagus,>0',
            'foto_bukti_rusak' => $this->status_penerimaan !== 'Diterima Penuh' ? 'required|image|max:2048' : 'nullable',
        ], [
            'detailTerima.*.id_lokasi.required_if' => 'Pilih Rak & Area untuk barang masuk.',
        ]);

        DB::transaction(function () {
            $pathFoto = $this->foto_bukti_rusak ? $this->foto_bukti_rusak->store('bukti-retur', 'public') : null;

            // 1. Insert Header Penerimaan
            // ID otomatis digenerate oleh method boot() di model PenerimaanMaterial
            $penerimaan = PenerimaanMaterial::create([
                'id_pengiriman'     => $this->id_pengiriman,
                'id_user_penerima'  => Auth::id() ?? 'USR001', 
                'tanggal_terima'    => $this->tanggal_terima,
                'status_penerimaan' => $this->status_penerimaan,
            ]);

            foreach ($this->detailTerima as $idDetDO => $item) {
                
                // (Catatan: Jika Model DetailPenerimaan juga sudah pakai boot() untuk ID DTR, 
                // baris $newIdDetailTerima ini dan kolom 'id_detail_terima' di bawah bisa Anda hapus)
                $newIdDetailTerima = 'DTR' . date('ymdHis') . rand(100, 999);

                // 2. Insert Detail Penerimaan
                DetailPenerimaan::create([
                    'id_detail_terima'     => $newIdDetailTerima,
                    'id_penerimaan'        => $penerimaan->id_penerimaan, // Mendapatkan ID otomatis dari model
                    'id_pengiriman_detail' => $idDetDO,
                    'id_detail_kontrak'    => $item['id_detail_kontrak'],
                    'jumlah_bagus'         => $item['jumlah_bagus'],
                    'jumlah_rusak'         => $item['jumlah_rusak'],
                    'alasan_return'        => $item['alasan_return'],
                    'foto_bukti_rusak'     => $pathFoto,
                ]);

                // 3. Insert ke Stok FIFO (Hanya barang yang bagus)
                if ((int)$item['jumlah_bagus'] > 0) {
                    
                    // (Catatan: Jika tabel stok_batch_fifo juga dibuatkan boot() Model, ID STK bisa dihapus)
                    $newIdStok = 'STK' . date('ymdHis') . rand(10, 99);

                    DB::table('stok_batch_fifo')->insert([
                        'id_stok'       => $newIdStok,
                        'id_material'   => $item['id_material'],
                        'id_penerimaan' => $penerimaan->id_penerimaan, // Mendapatkan ID otomatis dari model
                        'tanggal_masuk' => $this->tanggal_terima,
                        'jumlah_awal'   => (int)$item['jumlah_bagus'],
                        'sisa_stok'     => (int)$item['jumlah_bagus'],
                        'id_lokasi'     => $item['id_lokasi'], 
                        'status_stok'   => 'Tersedia',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // 4. UPDATE STATUS PENGIRIMAN
            $statusPengirimanUpdate = ($this->status_penerimaan == 'Diterima Penuh') ? 'Selesai' : 'Return & Kirim Ulang';

            Pengiriman::where('id_pengiriman', $this->id_pengiriman)->update([
                'status_pengiriman' => $statusPengirimanUpdate
            ]);
        });

        session()->flash('message', 'Sukses! Penerimaan dicatat dan Stok FIFO berhasil diperbarui.');
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
            // Relasi user ditambahkan kembali karena di Model sudah didefinisikan
            'listPenerimaan' => PenerimaanMaterial::with(['pengiriman', 'user']) 
                ->where('id_penerimaan', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10)
        ]);
    }
}