<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Role user yang sedang login
        $role = auth()->user()->role;

        // 2. Arahkan ke file view yang sesuai di folder 'dashboard'
        return match($role) {
            'Admin'         => view('dashboard.admin'),
            'Tim Proyek'    => view('dashboard.proyek'),
            'Tim Pengadaan' => view('dashboard.pengadaan'),
            'Top Manajemen' => view('dashboard.manajemen'),
            default         => view('dashboard'), // Jaga-jaga kalau role tidak dikenali
        };
    }
}