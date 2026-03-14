<?php

namespace App\Livewire\Pelaksanaan\Dashboard;

use Livewire\Component;
use App\Models\PermintaanProyek;
use App\Models\PenggunaanMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelaksanaanDashboard extends Component
{
    public function render()
    {
        // 1. Ambil ID User yang sedang login
        $userId = Auth::id();

        // 2. Cari ID proyek yang pernah dilaporkan/ditugaskan ke user ini
        $idProyekDitugaskan = PenggunaanMaterial::where('id_user_pelaksana', $userId)
                                ->pluck('id_proyek')
                                ->unique();

        // 3. STATISTIK CEPAT
        $totalLaporan = PenggunaanMaterial::where('id_user_pelaksana', $userId)->count();
        $totalPermintaan = PermintaanProyek::whereIn('id_proyek', $idProyekDitugaskan)->count();

        // 4. AKTIVITAS TERBARU (5 Data)
        $permintaanTerbaru = PermintaanProyek::with('proyek')
                                ->whereIn('id_proyek', $idProyekDitugaskan)
                                ->latest()
                                ->take(5)
                                ->get();

        $laporanTerbaru = PenggunaanMaterial::with('proyek')
                                ->where('id_user_pelaksana', $userId)
                                ->latest('tanggal_laporan')
                                ->take(5)
                                ->get();

        // 5. ANALISIS TREN (Top 5 Material Historis)
        $trenMaterial = DB::table('detail_penggunaan_material')
            ->join('penggunaan_material', 'detail_penggunaan_material.id_penggunaan', '=', 'penggunaan_material.id_penggunaan')
            ->join('material', 'detail_penggunaan_material.id_material', '=', 'material.id_material')
            ->where('penggunaan_material.id_user_pelaksana', $userId)
            ->select('material.nama_material', DB::raw('SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang'))
            ->groupBy('material.id_material', 'material.nama_material')
            ->orderByDesc('total_terpasang')
            ->limit(5)
            ->get();

        // Siapkan Array untuk Chart.js
        $chartLabels = $trenMaterial->pluck('nama_material')->toArray();
        $chartData = $trenMaterial->pluck('total_terpasang')->toArray();

        // 6. RETURN KE VIEW LIVEWIRE CHILD
        return view('livewire.pelaksanaan.dashboard.pelaksanaan-dashboard', [
            'totalLaporan'      => $totalLaporan,
            'totalPermintaan'   => $totalPermintaan,
            'permintaanTerbaru' => $permintaanTerbaru,
            'laporanTerbaru'    => $laporanTerbaru,
            'chartLabels'       => $chartLabels,
            'chartData'         => $chartData,
        ]);
    }
}