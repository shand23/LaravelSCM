<?php

use App\Http\Controllers\DashboardController; 
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Manajemen\Proyek\ProyekIndex;
use App\Livewire\Manajemen\Penugasan\PenugasanIndex; // <-- 1. Import komponen Penugasan
use Illuminate\Support\Facades\Route;

// ================= ROOT & AUTH =================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ================= DASHBOARD UTAMA (SATU UNTUK SEMUA) =================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Profile
    Route::view('/profile', 'profile')->name('profile');

    // ================= GRUP ROUTE ADMIN =================
    // (Akses: Mengelola User, RBAC, Backup)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserIndex::class)->name('users'); 
    });

    // ================= GRUP ROUTE TOP MANAJEMEN =================
    // (Akses: Kelola Proyek, Penugasan, Approval, Laporan)
    Route::prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/proyek', ProyekIndex::class)->name('proyek'); 
        Route::get('/penugasan', PenugasanIndex::class)->name('penugasan'); // <-- 2. Tambahkan route Penugasan di sini
    });

    // Nanti rute-rute lain untuk Tim Pengadaan, Logistik, dan Pelaksanaan bisa ditaruh di bawah sini
});

require __DIR__.'/auth.php';