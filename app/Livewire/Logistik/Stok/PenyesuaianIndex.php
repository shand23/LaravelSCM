<?php

namespace App\Livewire\Logistik\Stok;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PenyesuaianStok;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PenyesuaianIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJenis = '';

    // Variabel untuk Modal Foto
    public $isModalFotoOpen = false;
    public $fotoUrl = '';
    public $detailKeterangan = '';
    public $detailMaterial = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function lihatFoto($path, $keterangan, $namaMaterial)
    {
        $this->fotoUrl = asset('storage/' . $path);
        $this->detailKeterangan = $keterangan;
        $this->detailMaterial = $namaMaterial;
        $this->isModalFotoOpen = true;
    }

    public function closeModalFoto()
    {
        $this->isModalFotoOpen = false;
        $this->fotoUrl = '';
    }

    public function render()
    {
        // ==========================================
        // 1. DATA UNTUK GRAFIK (CHART.JS)
        // ==========================================
        
        // A. Grafik Donat: Komposisi Jenis Penyesuaian (All time atau bulan ini)
        $grafikJenis = PenyesuaianStok::select('jenis_penyesuaian', DB::raw('count(*) as total'))
            ->groupBy('jenis_penyesuaian')
            ->pluck('total', 'jenis_penyesuaian')
            ->toArray();

        // B. Grafik Garis: Tren Penyesuaian 30 Hari Terakhir
        $tigaPuluhHariLalu = Carbon::now()->subDays(30)->format('Y-m-d');
        $grafikTren = PenyesuaianStok::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>=', $tigaPuluhHariLalu)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->pluck('total', 'tanggal')
            ->toArray();

        // ==========================================
        // 2. DATA UNTUK TABEL LIST
        // ==========================================
        $query = PenyesuaianStok::with(['material', 'user'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id_penyesuaian', 'like', '%' . $this->search . '%')
                  ->orWhere('id_stok', 'like', '%' . $this->search . '%')
                  ->orWhereHas('material', function ($qMat) {
                      $qMat->where('nama_material', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->filterJenis)) {
            $query->where('jenis_penyesuaian', $this->filterJenis);
        }

        $riwayatPenyesuaian = $query->paginate(15);

        return view('livewire.logistik.stok.penyesuaian-index', [
            'riwayatPenyesuaian' => $riwayatPenyesuaian,
            'grafikJenis' => $grafikJenis,
            'grafikTren' => $grafikTren
        ]);
    }
}