<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

class AdminDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->ROLE !== 'Admin') {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.admin-dashboard')
            ->layout('layouts.app');
    }
}
