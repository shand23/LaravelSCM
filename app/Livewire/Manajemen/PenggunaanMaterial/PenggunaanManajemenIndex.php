<?php

namespace App\Livewire\Manajemen\PenggunaanMaterial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PenggunaanMaterial;
use App\Models\DetailPenggunaanMaterial;
use App\Models\Proyek;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PenggunaanManajemenIndex extends Component
{
    use WithPagination;

    // State untuk UI
    public $search = '';
    public $filterProyek = '';
    public $isModalDetailOpen = false;

    // Data untuk Lihat Detail
    public $laporanTerpilih = null;
    public $detailItems = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterProyek()
    {
        $this->resetPage();
    }

    // --- LOGIKA MODAL DETAIL ---
    public function bukaDetail($id)
    {
        $this->laporanTerpilih = PenggunaanMaterial::with(['proyek', 'permintaan', 'pelaksana'])->find($id);
        if ($this->laporanTerpilih) {
            $this->detailItems = DetailPenggunaanMaterial::with('material')->where('id_penggunaan', $id)->get();
            $this->isModalDetailOpen = true;
        }
    }

    public function tutupDetail()
    {
        $this->isModalDetailOpen = false;
        $this->laporanTerpilih = null;
    }

    public function render()
    {
        // Query Dasar: Mengambil SEMUA data untuk Manajemen
        $query = PenggunaanMaterial::with(['proyek', 'pelaksana', 'permintaan'])
            ->where(function($q) {
                $q->where('id_penggunaan', 'like', '%' . $this->search . '%')
                  ->orWhere('area_pekerjaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', function($sq) {
                      $sq->where('nama_proyek', 'like', '%' . $this->search . '%');
                  });
            });

        // Filter berdasarkan Proyek jika dipilih
        if ($this->filterProyek) {
            $query->where('id_proyek', $this->filterProyek);
        }

        return view('livewire.manajemen.penggunaan-material.penggunaan-manajemen-index', [
            'listLaporan' => $query->latest()->paginate(15),
            'listProyek' => Proyek::orderBy('nama_proyek', 'asc')->get()
        ]);
    }
}