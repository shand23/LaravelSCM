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

#[Layout('layouts.app')]
class PengadaanDashboard extends Component
{
    public function mount()
    {
        // Pengecekan keamanan role (Sesuai dengan ENUM di tabel users)
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
        
        // Pengiriman Aktif
        $totalPengiriman = Pengiriman::whereIn('status_pengiriman', ['Pending', 'Dalam Perjalanan'])->count();
        
        // Pengiriman Bermasalah (Retur)
        $totalRetur      = Pengiriman::where('status_pengiriman', 'Return & Kirim Ulang')->count();

        $totalInvoice    = InvoicePembelian::where('status_invoice', 'Menunggu Pembayaran')->count();

        // ==========================================
        // 2. GRAFIK TREN HARGA MATERIAL
        // ==========================================
        $dataTrenHarga = DB::table('detail_kontrak')
            ->join('material', 'detail_kontrak.id_material', '=', 'material.id_material')
            // DISESUAIKAN: Menggunakan kolom 'harga_negosiasi_satuan'
            ->select('material.nama_material', DB::raw('AVG(detail_kontrak.harga_negosiasi_satuan) as avg_harga'))
            ->groupBy('material.nama_material')
            ->orderBy('avg_harga', 'desc')
            ->take(5)
            ->get();

        $trenHargaMaterial = [
            'labels' => $dataTrenHarga->pluck('nama_material')->toArray(),
            'data'   => $dataTrenHarga->pluck('avg_harga')->toArray(),
        ];

        // ==========================================
        // 3. GRAFIK KINERJA SUPPLIER (Waktu Kirim)
        // ==========================================
        $dataKinerja = DB::table('pengiriman')
            ->join('kontrak', 'pengiriman.id_kontrak', '=', 'kontrak.id_kontrak')
            // DISESUAIKAN: Join langsung ke supplier (karena di tabel kontrak sudah ada id_supplier)
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

        // ==========================================
        // 4. POINT UTAMA SCM: KESEHATAN RANTAI PASOK
        // ==========================================
        $statusPengiriman = Pengiriman::select('status_pengiriman', DB::raw('count(*) as total'))
            ->groupBy('status_pengiriman')
            ->get();

        $kesehatanSCM = [
            'labels' => $statusPengiriman->pluck('status_pengiriman')->toArray(),
            'data'   => $statusPengiriman->pluck('total')->toArray(),
        ];

        // ==========================================
        // 5. ANALISA KEUANGAN (Nilai PO vs Tagihan)
        // ==========================================
        // DISESUAIKAN: Menggunakan 'total_nilai_kontrak' dari tabel kontrak
        $nilaiPOAktif = Kontrak::where('status_kontrak', 'Disepakati')
            ->sum('total_nilai_kontrak'); 
            
        // DISESUAIKAN: Menggunakan 'total_tagihan' dari tabel invoice_pembelian
        $nilaiInvoicePending = InvoicePembelian::where('status_invoice', 'Menunggu Pembayaran')
            ->sum('total_tagihan'); 
            
        $nilaiInvoiceLunas = InvoicePembelian::where('status_invoice', 'Lunas')
            ->sum('total_tagihan'); 

        $grafikKeuangan = [
            'labels' => ['Nilai PO Aktif', 'Tagihan (Belum Bayar)', 'Tagihan (Lunas)'],
            'data'   => [(float)$nilaiPOAktif, (float)$nilaiInvoicePending, (float)$nilaiInvoiceLunas],
        ];

        // ==========================================
        // Fallback jika database kosong 
        // ==========================================
        if (empty($trenHargaMaterial['labels'])) {
            $trenHargaMaterial = ['labels' => ['Belum Ada Data'], 'data' => [0]];
        }
        if (empty($kinerjaSupplier['labels'])) {
            $kinerjaSupplier = ['labels' => ['Belum Ada Data'], 'data' => [0]];
        }
        if (empty($kesehatanSCM['labels'])) {
            $kesehatanSCM = ['labels' => ['Belum Ada Data'], 'data' => [1]];
        }

        return view('livewire.pengadaan.dashboard.pengadaan-dashboard', [
            'totalSupplier'     => $totalSupplier,
            'totalPesanan'      => $totalPesanan,
            'totalKontrak'      => $totalKontrak,
            'totalPengiriman'   => $totalPengiriman,
            'totalInvoice'      => $totalInvoice,
            'totalRetur'        => $totalRetur, 
            'trenHargaMaterial' => $trenHargaMaterial,
            'kinerjaSupplier'   => $kinerjaSupplier,
            'kesehatanSCM'      => $kesehatanSCM,
            'grafikKeuangan'    => $grafikKeuangan,
        ]);
    }
}