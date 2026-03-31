<?php

namespace App\Livewire\Pengadaan\Dashboard;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Pesanan; 
use App\Models\Kontrak;
use App\Models\Pengiriman;
use App\Models\Invoice;

class PengadaanDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->ROLE !== 'Tim Pengadaan') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Tim Pengadaan.');
        }
    }

    public function render()
    {
        // 1. DATA METRIK KARTU (STATISTIK DASAR)
        $totalSupplier = class_exists(Supplier::class) ? Supplier::count() : 12; // Angka 12 hanya fallback dummy
        $totalPesanan = class_exists(Pesanan::class) ? Pesanan::count() : 45;
        $totalKontrak = class_exists(Kontrak::class) ? Kontrak::count() : 28;
        $totalPengiriman = class_exists(Pengiriman::class) ? Pengiriman::count() : 15;
        $totalInvoice = class_exists(Invoice::class) ? Invoice::count() : 10;

        // ==========================================
        // 2. DATA ANALISIS RANTAI PASOK (CHART.JS)
        // ==========================================
        
        // A. Data Dummy Tren Harga Material Utama (Misal: Semen Portland 50kg dalam 6 bulan terakhir)
        // NANTINYA GANTI DENGAN: Query rata-rata harga dari tabel detail_pesanan di-group by bulan
        $trenHargaMaterial = [
            'labels' => ['Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar'],
            'data' => [52000, 52500, 53000, 54500, 54000, 55000] 
        ];

        // B. Data Dummy Kinerja Supplier (Rata-rata Lead Time / Waktu Pengiriman dalam hitungan Hari)
        // NANTINYA GANTI DENGAN: Query selisih hari antara (tanggal_pesan) dan (tanggal_diterima) di-group by supplier
        $kinerjaSupplier = [
            'labels' => ['PT. Baja Nusantara', 'CV. Bangun Abadi', 'PT. Semen Jaya', 'UD. Kayu Mas', 'PT. Logam Kuat'],
            'data' => [3, 5, 2, 7, 4] // Waktu tunggu dalam hari (Makin kecil makin baik)
        ];

        return view('livewire.pengadaan.dashboard.pengadaan-dashboard', [
            'totalSupplier' => $totalSupplier,
            'totalPesanan' => $totalPesanan,
            'totalKontrak' => $totalKontrak,
            'totalPengiriman' => $totalPengiriman,
            'totalInvoice' => $totalInvoice,
            'trenHargaMaterial' => $trenHargaMaterial,
            'kinerjaSupplier' => $kinerjaSupplier,
        ]);
    }
}