<?php

namespace App\Livewire\Pengadaan\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Pesanan; 
use App\Models\Kontrak;
use App\Models\Pengiriman;
use App\Models\InvoicePembelian; 

class PengadaanDashboard extends Component
{
    public function mount()
    {
        // Pengecekan keamanan role
        if (auth()->user()->ROLE !== 'Tim Pengadaan') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Tim Pengadaan.');
        }
    }

    public function render()
    {
        // ==========================================
        // 1. DATA METRIK KARTU (STATISTIK DASAR)
        // ==========================================
        $totalSupplier   = Supplier::where('status_supplier', 'Aktif')->count();
        $totalPesanan    = Pesanan::whereIn('status_pesanan', ['Draft', 'Proses Negosiasi'])->count();
        $totalKontrak    = Kontrak::where('status_kontrak', 'Disepakati')->count();
        $totalPengiriman = Pengiriman::whereIn('status_pengiriman', ['Pending', 'Dalam Perjalanan'])->count();
        $totalInvoice    = InvoicePembelian::where('status_invoice', 'Menunggu Pembayaran')->count();
        
        // Metrik Identifikasi Masalah: Total Retur
        $totalReturn     = Kontrak::where('status_pengiriman', 'Return')->count();

        // ==========================================
        // 2. WIDGET 1: TREN HARGA MATERIAL (Analisis Biaya)
        // ==========================================
        // Mengambil rata-rata 'harga_negosiasi_satuan' per bulan dari kontrak yang sudah disepakati
        $dataHarga = DB::table('detail_kontrak')
            ->join('kontrak', 'detail_kontrak.id_kontrak', '=', 'kontrak.id_kontrak')
            ->select(
                DB::raw("DATE_FORMAT(kontrak.tanggal_kontrak, '%b %Y') as bulan"),
                DB::raw("AVG(detail_kontrak.harga_negosiasi_satuan) as rata_harga")
            )
            ->whereNotNull('kontrak.tanggal_kontrak')
            ->where('kontrak.status_kontrak', 'Disepakati')
            ->groupBy('bulan', DB::raw("YEAR(kontrak.tanggal_kontrak)"), DB::raw("MONTH(kontrak.tanggal_kontrak)"))
            ->orderBy(DB::raw("YEAR(kontrak.tanggal_kontrak)"), 'asc')
            ->orderBy(DB::raw("MONTH(kontrak.tanggal_kontrak)"), 'asc')
            ->take(6)
            ->get();

        $trenHargaMaterial = [
            'labels' => $dataHarga->pluck('bulan')->toArray(),
            'data'   => $dataHarga->pluck('rata_harga')->toArray(),
        ];

        // ==========================================
        // 3. WIDGET 2: KINERJA SUPPLIER (Waktu Tunggu / Lead Time)
        // ==========================================
        // Menghitung selisih hari pengiriman dari tiap supplier
        $dataKinerja = DB::table('pengiriman')
            ->join('kontrak', 'pengiriman.id_kontrak', '=', 'kontrak.id_kontrak')
            ->join('supplier', 'kontrak.id_supplier', '=', 'supplier.id_supplier')
            ->select(
                'supplier.nama_supplier',
                DB::raw("AVG(DATEDIFF(pengiriman.estimasi_tanggal_tiba, pengiriman.tanggal_berangkat)) as lead_time")
            )
            ->whereNotNull('pengiriman.tanggal_berangkat')
            ->whereNotNull('pengiriman.estimasi_tanggal_tiba')
            ->groupBy('supplier.nama_supplier')
            ->orderBy('lead_time', 'asc')
            ->take(5)
            ->get();

        $kinerjaSupplier = [
            'labels' => $dataKinerja->pluck('nama_supplier')->toArray(),
            'data'   => $dataKinerja->pluck('lead_time')->toArray(),
        ];

        // Fallback jika database masih kosong agar grafik tidak error (Data Dummy Sementara)
        if (empty($trenHargaMaterial['labels'])) {
            $trenHargaMaterial = ['labels' => ['Belum Ada Data'], 'data' => [0]];
        }
        if (empty($kinerjaSupplier['labels'])) {
            $kinerjaSupplier = ['labels' => ['Belum Ada Data'], 'data' => [0]];
        }

        return view('livewire.pengadaan.dashboard.pengadaan-dashboard', [
            'totalSupplier'   => $totalSupplier,
            'totalPesanan'    => $totalPesanan,
            'totalKontrak'    => $totalKontrak,
            'totalPengiriman' => $totalPengiriman,
            'totalInvoice'    => $totalInvoice,
            'totalReturn'     => $totalReturn,
            'trenHargaMaterial' => $trenHargaMaterial,
            'kinerjaSupplier'   => $kinerjaSupplier,
        ]);
    }
}