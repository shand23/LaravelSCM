<?php

namespace App\Livewire\Logistik\Stok;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // Import trait untuk upload file
use Livewire\Attributes\Layout;
use App\Models\StokBatchFifo;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class StokIndex extends Component
{
    use WithPagination, WithFileUploads; // Gunakan trait di dalam class

    public $search = '';

    // Variabel untuk Modal Batch
    public $isModalBatchOpen = false;
    public $detailBatches = [];
    public $namaMaterialModal = '';
    public $kategoriMaterialModal = '';
    public $totalStokModal = 0;

    // Variabel untuk Fitur Lapor Rusak (Adjustment)
    public $isModalAdjustmentOpen = false;
    public $adj_id_stok, $adj_id_material, $adj_max_stok;
    public $adj_jumlah, $adj_jenis = 'Rusak', $adj_keterangan;
    public $adj_bukti_foto; // Properti untuk menampung file upload

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function lihatBatch($id_material)
    {
        $this->detailBatches = StokBatchFifo::with(['lokasiRak', 'material.kategori'])
            ->where('id_material', $id_material)
            ->where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk', 'asc') 
            ->get();

        if($this->detailBatches->count() > 0) {
            $firstBatch = $this->detailBatches->first();
            $this->namaMaterialModal = $firstBatch->material->nama_material ?? 'Unknown';
            $this->kategoriMaterialModal = $firstBatch->material->kategori->nama_kategori ?? 'Umum';
            $this->totalStokModal = $this->detailBatches->sum('sisa_stok');
            
            $this->isModalBatchOpen = true;
        } else {
            $this->isModalBatchOpen = false;
        }
    }

    public function closeModal()
    {
        $this->isModalBatchOpen = false;
        $this->detailBatches = [];
    }

    // Membuka modal form penyesuaian/lapor rusak
    public function openAdjustment($id_stok, $id_material, $sisa_stok)
    {
        $this->adj_id_stok = $id_stok;
        $this->adj_id_material = $id_material;
        $this->adj_max_stok = $sisa_stok;
        
        // Reset form setiap kali dibuka
        $this->adj_jumlah = '';
        $this->adj_keterangan = '';
        $this->adj_jenis = 'Rusak';
        $this->adj_bukti_foto = null; 
        
        $this->isModalAdjustmentOpen = true;
    }

    public function closeAdjustment()
    {
        $this->isModalAdjustmentOpen = false;
        $this->adj_bukti_foto = null; // Hapus file preview dari memori
        $this->resetValidation();
    }

    public function submitAdjustment()
    {
        // Validasi input termasuk file gambar (maks 2MB)
        $this->validate([
            'adj_jumlah' => 'required|numeric|min:1|max:' . $this->adj_max_stok,
            'adj_jenis' => 'required|in:Rusak,Hilang,Kadaluarsa,Selisih Opname',
            'adj_keterangan' => 'required|string|max:255',
            'adj_bukti_foto' => 'nullable|image|max:2048', 
        ]);

        DB::beginTransaction();
        try {
            // Proses Upload Foto ke Storage Lokal (storage/app/public/bukti_penyesuaian)
            $fotoPath = null;
            if ($this->adj_bukti_foto) {
                $fotoPath = $this->adj_bukti_foto->store('bukti_penyesuaian', 'public');
            }

            // Buat ID unik untuk laporan penyesuaian
            $id_penyesuaian = 'ADJ-' . date('Ymd-His'); 
            
            // Simpan log ke tabel penyesuaian_stok
            DB::table('penyesuaian_stok')->insert([
                'id_penyesuaian' => $id_penyesuaian,
                'id_stok' => $this->adj_id_stok,
                'id_material' => $this->adj_id_material,
                'id_user' => Auth::user()->id_user ?? 'SYSTEM', // Pastikan Auth sesuai dengan sistem Anda
                'jenis_penyesuaian' => $this->adj_jenis,
                'jumlah_penyesuaian' => $this->adj_jumlah,
                'keterangan' => $this->adj_keterangan,
                'bukti_foto' => $fotoPath, // Path foto disimpan ke database
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kurangi stok di batch yang bersangkutan
            $batch = StokBatchFifo::where('id_stok', $this->adj_id_stok)->first();
            $batch->sisa_stok -= $this->adj_jumlah;
            
            // Ubah status jika stok habis
            if ($batch->sisa_stok <= 0) {
                $batch->status_stok = 'Habis';
                $batch->sisa_stok = 0;
            }
            $batch->save();

            DB::commit();

            session()->flash('success', 'Laporan stok rusak/penyesuaian berhasil dicatat.');
            
            $this->closeAdjustment();
            
            // Refresh data di modal batch agar stoknya update
            $this->lihatBatch($this->adj_id_material);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $listStok = Material::with('kategori')
            ->leftJoin('stok_batch_fifo', function($join) {
                $join->on('material.id_material', '=', 'stok_batch_fifo.id_material')
                     ->where('stok_batch_fifo.sisa_stok', '>', 0); 
            })
            ->select(
                'material.id_material',
                'material.nama_material',
                'material.id_kategori_material',
                DB::raw('COALESCE(SUM(stok_batch_fifo.sisa_stok), 0) as total_sisa'), 
                DB::raw('MIN(stok_batch_fifo.tanggal_masuk) as tgl_terlama'),
                DB::raw('COUNT(stok_batch_fifo.id_stok) as jumlah_batch')
            )
            ->where(function($query) {
                $query->where('material.nama_material', 'like', '%' . $this->search . '%')
                      ->orWhere('material.id_material', 'like', '%' . $this->search . '%');
            })
            ->groupBy('material.id_material', 'material.nama_material', 'material.id_kategori_material')
            ->orderBy('total_sisa', 'asc') 
            ->paginate(12);

        return view('livewire.logistik.stok.stok-index', [
            'listStok' => $listStok
        ]);
    }
}