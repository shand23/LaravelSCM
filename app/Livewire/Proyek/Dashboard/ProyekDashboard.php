<?php

namespace App\Livewire\Proyek\Dashboard;

use Livewire\Component;

class ProyekDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->role !== 'Tim Proyek') {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.proyek.dashboard.proyek-dashboard')
            ->layout('layouts.app');
    }
}
