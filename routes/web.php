<?php

use App\Http\Controllers\DashboardController; 
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Manajemen\Proyek\ProyekIndex;
use App\Livewire\Manajemen\Penugasan\PenugasanIndex; 
use App\Livewire\Logistik\Kategori\KategoriIndex; 
use App\Livewire\Logistik\Material\MaterialIndex;
use App\Livewire\Pelaksanaan\PermintaanProyek\IndexPermintaan;
use App\Livewire\Pengadaan\Supplier\SupplierIndex;
use App\Livewire\Manajemen\Approval\ApprovalIndex;

// Pengajuan Pembelian (PR)
use App\Livewire\Logistik\PengajuanPembelian\PengajuanIndex as LogistikPengajuanIndex;

// --- IMPORT COMPONENT PENERIMAAN (BARU) ---
use App\Livewire\Logistik\Penerimaan\PenerimaanIndex; 

// --- IMPORT COMPONENT PENGADAAN (RFQ, KONTRAK & PENGIRIMAN) ---
use App\Livewire\Pengadaan\Pesanan\PesananIndex;
use App\Livewire\Pengadaan\Kontrak\KontrakIndex;
use App\Livewire\Pengadaan\Pengiriman\PengirimanIndex; 

use Illuminate\Support\Facades\Route;

// ================= ROOT & AUTH =================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ================= DASHBOARD UTAMA =================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Profile
    Route::view('/profile', 'profile')->name('profile');

    // ================= GRUP ROUTE ADMIN =================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserIndex::class)->name('users'); 
    });

    // ================= GRUP ROUTE TOP MANAJEMEN =================
    Route::prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/proyek', ProyekIndex::class)->name('proyek'); 
        Route::get('/penugasan', PenugasanIndex::class)->name('penugasan');
    });

    // ================= GRUP ROUTE TIM PELAKSANAAN =================
    Route::prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        Route::get('/permintaan', IndexPermintaan::class)->name('permintaan');
    });

    // ================= GRUP ROUTE LOGISTIK =================
    Route::prefix('logistik')->name('logistik.')->group(function () {
        Route::get('/kategori', KategoriIndex::class)->name('kategori'); 
        Route::get('/material', MaterialIndex::class)->name('material');
        
        // Pengajuan Pembelian (PR)
        Route::get('/pengajuan', LogistikPengajuanIndex::class)->name('pengajuan');

        // --- PROSES PENERIMAAN MATERIAL (RECEIVING) ---
        Route::get('/penerimaan', PenerimaanIndex::class)->name('penerimaan'); // <--- Tambahan Baru
    });

    // ================= GRUP ROUTE TIM PENGADAAN =================
    Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
        Route::get('/supplier', SupplierIndex::class)->name('supplier');
        
        // --- PROSES RFQ (REQUEST FOR QUOTATION) ---
        Route::get('/pesanan', PesananIndex::class)->name('pesanan');

        // --- PROSES KONTRAK & PURCHASE ORDER (PO) ---
        Route::get('/kontrak', KontrakIndex::class)->name('kontrak');

        // --- PROSES PENGIRIMAN MATERIAL DARI SUPPLIER ---
        Route::get('/pengiriman', PengirimanIndex::class)->name('pengiriman'); 
    });

});

require __DIR__.'/auth.php';