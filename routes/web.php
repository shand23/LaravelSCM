<?php

use App\Http\Controllers\DashboardController; 
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Admin\Proyek\ProyekIndex;
use App\Livewire\Admin\Proyek\PenugasanIndex;
use App\Livewire\Admin\Kategori\KategoriIndex;
use App\Livewire\Admin\Material\MaterialIndex;
use App\Livewire\Admin\Supplier\SupplierIndex; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Arahkan Halaman Depan (Root) ke Login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Route Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route Profile
    Route::view('/profile', 'profile')->name('profile');

    /*
    |--------------------------------------------------------------------------
    | GROUP ROUTE ADMIN
    | URL Prefix: /admin/...
    | Route Name Prefix: admin....
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // --- MANAJEMEN USER ---
        Route::get('/users', UserIndex::class)->name('users'); 

        // --- MANAJEMEN PROYEK & TIM ---
        Route::get('/proyek', ProyekIndex::class)->name('proyek');
        Route::get('/penugasan', PenugasanIndex::class)->name('penugasan');

        // --- LOGISTIK & MATERIAL ---
        Route::get('/supplier', SupplierIndex::class)->name('supplier'); // <--- Route Baru
        Route::get('/kategori-material', KategoriIndex::class)->name('kategori');    
        Route::get('/material', MaterialIndex::class)->name('material');

    });
});

require __DIR__.'/auth.php';