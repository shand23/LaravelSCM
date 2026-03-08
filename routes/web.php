<?php

use App\Http\Controllers\DashboardController; 
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Manajemen\Proyek\ProyekIndex;
use App\Livewire\Manajemen\Penugasan\PenugasanIndex; 
use App\Livewire\Logistik\Kategori\KategoriIndex; 
use App\Livewire\Logistik\Material\MaterialIndex;
use App\Livewire\Pengadaan\Supplier\SupplierIndex;
use Illuminate\Support\Facades\Route;

// ================= ROOT & AUTH =================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ================= DASHBOARD UTAMA (SATU UNTUK SEMUA ROLE) =================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Profile
    Route::view('/profile', 'profile')->name('profile');

    // ================= GRUP ROUTE ADMIN =================
    // (Akses: Mengelola User, RBAC, System)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserIndex::class)->name('users'); 
    });

    // ================= GRUP ROUTE TOP MANAJEMEN =================
    // (Akses: Kelola Proyek, Penugasan, Approval, Laporan)
    Route::prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/proyek', ProyekIndex::class)->name('proyek'); 
        Route::get('/penugasan', PenugasanIndex::class)->name('penugasan'); 
    });

    // ================= GRUP ROUTE TIM PELAKSANAAN =================
    // (Akses: Proyek Saya, Laporan Progress Harian, dll)
    Route::prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        // Nanti route untuk modul Tim Pelaksanaan ditambahkan di sini
        // Route::get('/proyek-saya', ...)->name('proyek_saya');
    });

    // ================= GRUP ROUTE LOGISTIK =================
    // (Akses khusus role Logistik: Master Data Kategori, Material, Inventory, dll)
    Route::prefix('logistik')->name('logistik.')->group(function () {
        Route::get('/kategori', KategoriIndex::class)->name('kategori'); // Route Master Kategori
        Route::get('/material', MaterialIndex::class)->name('material');
        // Nanti CRUD Material bisa langsung ditambahkan di bawah sini:
        // Route::get('/material', MaterialIndex::class)->name('material');
    });

    // ================= GRUP ROUTE TIM PENGADAAN =================
    // (Akses: Kelola Supplier, Purchase Order (PO), dll)
    Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
        Route::get('/supplier', SupplierIndex::class)->name('supplier');
    });

});

require __DIR__.'/auth.php';