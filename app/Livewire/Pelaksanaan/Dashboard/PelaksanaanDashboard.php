<?php

namespace App\Livewire\Pelaksanaan\Dashboard;

use Livewire\Component;

class PelaksanaanDashboard extends Component
{
    public function mount()
    {
        // Proteksi keamanan: pastikan hanya role Tim Pelaksanaan yang bisa mengakses
        if (auth()->user()->ROLE !== 'Tim Pelaksanaan') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Tim Pelaksanaan.');
        }
    }

    public function render()
    {
        // Path disesuaikan menunjuk ke folder dashboard
        return view('dashboard.pelaksanaan')
            ->layout('layouts.app');
    }
}