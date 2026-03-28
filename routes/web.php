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
use App\Livewire\Logistik\Stok\StokIndex;

// Pengajuan Pembelian (PR)
use App\Livewire\Logistik\PengajuanPembelian\PengajuanIndex as LogistikPengajuanIndex;

// --- IMPORT COMPONENT LOGISTIK ---
use App\Livewire\Logistik\Penerimaan\PenerimaanIndex; 
use App\Livewire\Logistik\PermintaanProyek\PermintaanProyekLogistikIndex; 

// --- IMPORT COMPONENT PENGADAAN (RFQ, KONTRAK & PENGIRIMAN) ---
use App\Livewire\Pengadaan\Pesanan\PesananIndex;
use App\Livewire\Pengadaan\Kontrak\KontrakIndex;
use App\Livewire\Pengadaan\Pengiriman\PengirimanIndex; 

// --- IMPORT COMPONENT PELAKSANAAN ---
use App\Livewire\Pelaksanaan\PenggunaanMaterial\PenggunaanIndex as PelaksanaanPenggunaanIndex; 
use App\Livewire\Pelaksanaan\Penugasan\PenugasanProyekIndex; // <--- Import baru untuk Penugasan Proyek (Proyek Saya)

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
        
        // --- HALAMAN APPROVAL PERMINTAAN MATERIAL ---
        Route::get('/approval', ApprovalIndex::class)->name('approval'); 
    });

    // ================= GRUP ROUTE TIM PELAKSANAAN =================
    Route::prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        
        // --- HALAMAN PROYEK SAYA (PENUGASAN) BARU ---
        Route::get('/proyek-saya', PenugasanProyekIndex::class)->name('proyek-saya'); // <--- Route baru
        
        // Halaman request barang oleh pelaksana
        Route::get('/permintaan', IndexPermintaan::class)->name('permintaan');

        // --- LAPORAN PENGGUNAAN MATERIAL ---
        Route::get('/penggunaan', PelaksanaanPenggunaanIndex::class)->name('penggunaan'); 
    });

    // ================= GRUP ROUTE LOGISTIK =================
    Route::prefix('logistik')->name('logistik.')->group(function () {
        Route::get('/kategori', KategoriIndex::class)->name('kategori'); 
        Route::get('/material', MaterialIndex::class)->name('material');
        
        // Pengajuan Pembelian (PR)
        Route::get('/pengajuan', LogistikPengajuanIndex::class)->name('pengajuan');

        // --- PROSES PENERIMAAN MATERIAL (RECEIVING) ---
        Route::get('/penerimaan', PenerimaanIndex::class)->name('penerimaan');

        // --- PROSES MONITORING STOK ---
        Route::get('/stok', StokIndex::class)->name('stok');

        // --- PROSES PEMENUHAN PERMINTAAN PROYEK ---
        Route::get('/permintaan-proyek', PermintaanProyekLogistikIndex::class)->name('permintaan-proyek'); 
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