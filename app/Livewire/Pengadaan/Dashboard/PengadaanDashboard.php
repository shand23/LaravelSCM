<?php

namespace App\Livewire\Pengadaan\Dashboard;

use Livewire\Component;

class PengadaanDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->role !== 'Tim Pengadaan') {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.pengadaan.dashboard.pengadaan-dashboard')
            ->layout('layouts.app');
    }
}
