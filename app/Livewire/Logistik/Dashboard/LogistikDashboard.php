<?php

namespace App\Livewire\Logistik\Dashboard;

use Livewire\Component;

class LogistikDashboard extends Component
{
    public function mount()
    {
        // Proteksi keamanan: pastikan hanya role Logistik yang bisa mengakses
        if (auth()->user()->ROLE !== 'Logistik') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Tim Logistik.');
        }
    }

    public function render()
    {
        return view('livewire.logistik.dashboard.logistik-dashboard')
            ->layout('layouts.app');
    }
}