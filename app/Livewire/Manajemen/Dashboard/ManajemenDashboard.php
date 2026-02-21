<?php

namespace App\Livewire\Manajemen\Dashboard;

use Livewire\Component;

class ManajemenDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->role !== 'Top Manajemen') {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.manajemen.dashboard.manajemen-dashboard')
            ->layout('layouts.app');
    }
}
