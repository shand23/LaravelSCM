<?php

use App\Http\Controllers\DashboardController; 
use App\Livewire\Admin\User\UserIndex; // Pastikan ini sesuai dengan nama file Anda
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

    // ================= GRUP ROUTE ADMIN (Selain Dashboard) =================
    Route::prefix('admin')->name('admin.')->group(function () {
        // Rute untuk manajemen user tetap pakai Livewire
        Route::get('/users', UserIndex::class)->name('users'); 
    });

    // Nanti rute-rute lain untuk Tim Pengadaan, dll (selain dashboard) bisa ditaruh di sini
});

require __DIR__.'/auth.php';