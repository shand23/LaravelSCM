<?php

namespace App\Livewire\Logistik\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Material;
use App\Models\PengajuanPembelian;
use App\Models\PenerimaanMaterial;
use App\Models\PermintaanProyek;
use App\Models\PenyesuaianStok;

class LogistikDashboard extends Component
{
    public function mount()
    {
        if (auth()->user()->ROLE !== 'Logistik') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Tim Logistik.');
        }
    }

    public function render()
    {
        // ==========================================
        // 1. DATA METRIK KARTU (STATISTIK DASAR)
        // ==========================================
        $totalMaterial = class_exists(Material::class) ? Material::count() : 0;
        
        $totalPengajuan = class_exists(PengajuanPembelian::class) 
            ? PengajuanPembelian::whereIn('status_pengajuan', ['Draft', 'Diajukan', 'Menunggu Pengadaan'])->count() : 0;
            
        $totalPenerimaanPending = class_exists(PenerimaanMaterial::class) 
            ? PenerimaanMaterial::whereIn('status_penerimaan', ['Diterima Sebagian', 'Return'])->count() : 0;
            
        $totalPermintaanPending = class_exists(PermintaanProyek::class) 
            ? PermintaanProyek::whereIn('status_permintaan', ['Menunggu Persetujuan', 'Diproses Sebagian'])->count() : 0;

        // PERBAIKAN ERROR: Menggunakan 'created_at' bukan 'tanggal_penyesuaian'
        $totalPenyesuaian = class_exists(PenyesuaianStok::class) 
            ? PenyesuaianStok::whereMonth('created_at', now()->month)->count() : 0;

        // ==========================================
        // 2. WIDGET 1: ALUR BARANG MASUK VS KELUAR 
        // ==========================================
        // A. Barang Masuk (Dari detail_penerimaan -> jumlah_bagus)
        $dataMasukRaw = DB::table('detail_penerimaan')
            ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
            ->select(
                DB::raw("DATE_FORMAT(penerimaan_material.tanggal_terima, '%b') as bulan"),
                DB::raw("SUM(detail_penerimaan.jumlah_bagus) as total_masuk")
            )
            ->whereNotNull('penerimaan_material.tanggal_terima')
            ->groupBy('bulan', DB::raw("MONTH(penerimaan_material.tanggal_terima)"))
            ->orderBy(DB::raw("MONTH(penerimaan_material.tanggal_terima)"), 'asc')
            ->take(6)
            ->pluck('total_masuk', 'bulan')->toArray();

        // B. Barang Keluar (Dari pengeluaran_stok_fifo -> jumlah_diambil)
        // Menggunakan created_at karena tidak ada tanggal spesifik di tabel ini
        $dataKeluarRaw = DB::table('pengeluaran_stok_fifo')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b') as bulan"),
                DB::raw("SUM(jumlah_diambil) as total_keluar")
            )
            ->whereNotNull('created_at')
            ->groupBy('bulan', DB::raw("MONTH(created_at)"))
            ->orderBy(DB::raw("MONTH(created_at)"), 'asc')
            ->take(6)
            ->pluck('total_keluar', 'bulan')->toArray();

        // Menggabungkan Label Bulan (Agar sejajar di grafik)
        $semuaBulan = array_unique(array_merge(array_keys($dataMasukRaw), array_keys($dataKeluarRaw)));
        $labelsAlur = [];
        $masukFinal = [];
        $keluarFinal= [];

        foreach ($semuaBulan as $bln) {
            $labelsAlur[] = $bln;
            $masukFinal[] = $dataMasukRaw[$bln] ?? 0;
            $keluarFinal[]= $dataKeluarRaw[$bln] ?? 0;
        }

        $alurBarang = [
            'labels' => empty($labelsAlur) ? ['Belum Ada Data'] : $labelsAlur,
            'masuk'  => empty($masukFinal) ? [0] : $masukFinal,
            'keluar' => empty($keluarFinal) ? [0] : $keluarFinal,
        ];

        // ==========================================
        // 3. WIDGET 2: TREN PENYESUAIAN STOK (BAR CHART)
        // ==========================================
        // PERBAIKAN ERROR: Menggunakan 'created_at'
        $dataPenyesuaian = DB::table('penyesuaian_stok')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b') as bulan"),
                DB::raw("COUNT(id_penyesuaian) as total_kasus")
            )
            ->whereNotNull('created_at')
            ->groupBy('bulan', DB::raw("MONTH(created_at)"))
            ->orderBy(DB::raw("MONTH(created_at)"), 'asc')
            ->take(6)
            ->get();

        $trenPenyesuaian = [
            'labels' => $dataPenyesuaian->pluck('bulan')->toArray(),
            'data'   => $dataPenyesuaian->pluck('total_kasus')->toArray(),
        ];

        // Fallback jika database masih kosong
        if (empty($trenPenyesuaian['labels'])) {
            $trenPenyesuaian = ['labels' => ['Belum Ada Data'], 'data' => [0]];
        }

        return view('livewire.logistik.dashboard.logistik-dashboard', [
            'totalMaterial'          => $totalMaterial,
            'totalPengajuan'         => $totalPengajuan,
            'totalPenerimaanPending' => $totalPenerimaanPending,
            'totalPermintaanPending' => $totalPermintaanPending,
            'totalPenyesuaian'       => $totalPenyesuaian,
            'alurBarang'             => $alurBarang,
            'trenPenyesuaian'        => $trenPenyesuaian,
        ]);
    }
}