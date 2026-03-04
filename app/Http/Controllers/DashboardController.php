<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Role user yang sedang login
        $ROLE = auth()->user()->ROLE;

        // 2. Arahkan ke file view yang sesuai di folder 'dashboard'
        return match($ROLE) {
            'Admin'           => view('dashboard.admin'),
            'Tim Pengadaan'   => view('dashboard.pengadaan'),
            'Tim Pelaksanaan' => view('dashboard.pelaksanaan'),
            'Logistik'        => view('dashboard.logistik'),
            'Top Manajemen'   => view('dashboard.manajemen'),
            default           => view('dashboard'), // Jaga-jaga kalau role tidak dikenali
        };
    }
}