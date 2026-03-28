<?php

namespace App\Livewire\Manajemen\Approval;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PermintaanProyek;
use App\Models\Proyek; // Pastikan Model Proyek sudah di-import

#[Layout('layouts.app')] 
class ApprovalIndex extends Component
{
    use WithPagination;

    // Properti untuk Pencarian dan Filter
    public $search = '';
    public $filterProyek = '';
    public $filterStatus = 'Menunggu Persetujuan'; // Default yang tampil adalah yang butuh persetujuan
    
    // Properti untuk Sorting
    public $sortColumn = 'tanggal_permintaan';
    public $sortDirection = 'desc';

    // Properti untuk Modal Detail
    public $permintaanDipilih; 
    public $isModalOpen = false; 

    // Reset halaman ke 1 jika user mengetik pencarian atau mengganti filter
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProyek() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    // Fungsi untuk Sorting tabel
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = PermintaanProyek::with('proyek');

        // 1. Terapkan Filter Status
        if ($this->filterStatus !== '') {
            $query->where('status_permintaan', $this->filterStatus);
        }

        // 2. Terapkan Filter Proyek
        if ($this->filterProyek !== '') {
            $query->where('id_proyek', $this->filterProyek);
        }

        // 3. Terapkan Pencarian (Search)
        if ($this->search !== '') {
            $query->where(function($q) {
                $q->where('id_permintaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', function($subQ) {
                      $subQ->where('nama_proyek', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // 4. Terapkan Sorting dan Pagination
        $dataPermintaan = $query->orderBy($this->sortColumn, $this->sortDirection)->paginate(10);
        
        // Ambil data proyek untuk dropdown filter
        $daftarProyek = Proyek::all();

        return view('livewire.manajemen.approval.approval-index', [
            'dataPermintaan' => $dataPermintaan,
            'daftarProyek' => $daftarProyek
        ]);
    }

    // ================= FUNGSI MODAL & AKSI =================

    public function lihatDetail($id)
    {
        $this->permintaanDipilih = PermintaanProyek::with(['proyek', 'detailPermintaan.material'])
                                        ->where('id_permintaan', $id)
                                        ->first();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->permintaanDipilih = null;
    }

    public function approve($id)
    {
        $permintaan = PermintaanProyek::where('id_permintaan', $id)->first();
        if ($permintaan) {
            $permintaan->update(['status_permintaan' => 'Disetujui PM']); 
            $this->closeModal();
            session()->flash('success', 'Permintaan #'.$id.' berhasil disetujui!');
        }
    }

    public function tolak($id)
    {
        $permintaan = PermintaanProyek::where('id_permintaan', $id)->first();
        if ($permintaan) {
            $permintaan->update(['status_permintaan' => 'Ditolak']);
            $this->closeModal();
            session()->flash('error', 'Permintaan #'.$id.' telah ditolak.');
        }
    }
}