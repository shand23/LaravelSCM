<?php

namespace App\Livewire\Logistik\Stok;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\StokBatchFifo;
use App\Models\Material; // <-- Pastikan Model Material di-import
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class StokIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Variabel untuk Modal Batch
    public $isModalBatchOpen = false;
    public $detailBatches = [];
    public $namaMaterialModal = '';
    public $kategoriMaterialModal = '';
    public $totalStokModal = 0;

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
        }
    }

    public function closeModal()
    {
        $this->isModalBatchOpen = false;
        $this->detailBatches = [];
    }

    public function render()
    {
        // 1. Jadikan Material sebagai Base Query
        // 2. Gunakan Left Join agar material bersisa stok 0 tetap tampil
        $listStok = Material::with('kategori')
            ->leftJoin('stok_batch_fifo', function($join) {
                $join->on('material.id_material', '=', 'stok_batch_fifo.id_material')
                     ->where('stok_batch_fifo.sisa_stok', '>', 0); // Hanya hitung stok yg belum habis
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
            ->orderBy('total_sisa', 'asc') // Urutkan dari stok paling sedikit/0 agar terlihat duluan
            ->paginate(12);

        return view('livewire.logistik.stok.stok-index', [
            'listStok' => $listStok
        ]);
    }
}