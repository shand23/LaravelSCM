<?php

namespace App\Livewire\Pelaksanaan\Penugasan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PenugasanProyek;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PenugasanProyekIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Mengambil ID user yang sedang login (fallback ke USR001 jika testing)
        $userId = Auth::id() ?? 'USR001'; 

        $query = PenugasanProyek::with('proyek')
            ->where('id_user', $userId)
            ->where(function($q) {
                $q->whereHas('proyek', function($q2) {
                    $q2->where('nama_proyek', 'like', '%' . $this->search . '%')
                      ->orWhere('id_proyek', 'like', '%' . $this->search . '%');
                });
            });

        if ($this->filterStatus !== '') {
            $query->where('status_penugasan', $this->filterStatus);
        }

        return view('livewire.pelaksanaan.penugasan.penugasan-proyek-index', [
            // Menampilkan 6 data per halaman agar grid terlihat seimbang
            'listPenugasan' => $query->orderBy('tanggal_mulai', 'desc')->paginate(6) 
        ]);
    }
}