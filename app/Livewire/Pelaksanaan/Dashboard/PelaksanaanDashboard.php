<?php

namespace App\Livewire\Pelaksanaan\Dashboard;

use Livewire\Component;
use App\Models\PermintaanProyek;
use App\Models\PenggunaanMaterial;
use App\Models\PenugasanProyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelaksanaanDashboard extends Component
{
    public $selectedProyek = ''; // Variabel untuk menyimpan filter dropdown

    public function render()
    {
        // 1. Ambil ID User yang sedang login
        $userId = Auth::id() ?? 'USR001';

        // 2. Ambil DAFTAR PENUGASAN (untuk Dropdown)
        $listPenugasan = PenugasanProyek::with('proyek')
                            ->where('id_user', $userId)
                            ->where('status_penugasan', 'Aktif')
                            ->get();

        // 3. Tentukan target ID Proyek yang akan dihitung 
        // Jika dropdown dipilih, gunakan id tersebut. Jika kosong, gunakan semua id penugasannya.
        if ($this->selectedProyek != '') {
            $proyekTarget = [$this->selectedProyek];
        } else {
            $proyekTarget = $listPenugasan->pluck('id_proyek')->toArray();
        }

        // 4. STATISTIK CEPAT (Berdasarkan Proyek Target)
        $totalLaporan = PenggunaanMaterial::whereIn('id_proyek', $proyekTarget)
                            ->where('id_user_pelaksana', $userId)
                            ->count();
                            
        $totalPermintaan = PermintaanProyek::whereIn('id_proyek', $proyekTarget)->count();

        // 5. AKTIVITAS TERBARU (5 Data)
        // Laporan Penggunaan
        $laporanTerbaru = PenggunaanMaterial::with('proyek')
                            ->whereIn('id_proyek', $proyekTarget)
                            ->where('id_user_pelaksana', $userId)
                            ->latest('tanggal_laporan')
                            ->take(5)
                            ->get();

        // 6. ANALISIS TREN (Top 5 Material Historis)
        $trenMaterial = DB::table('detail_penggunaan_material')
            ->join('penggunaan_material', 'detail_penggunaan_material.id_penggunaan', '=', 'penggunaan_material.id_penggunaan')
            ->join('material', 'detail_penggunaan_material.id_material', '=', 'material.id_material')
            ->whereIn('penggunaan_material.id_proyek', $proyekTarget)
            ->where('penggunaan_material.id_user_pelaksana', $userId)
            ->select('material.nama_material', DB::raw('SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang'))
            ->groupBy('material.id_material', 'material.nama_material')
            ->orderByDesc('total_terpasang')
            ->limit(5)
            ->get();

        // Siapkan Array untuk Chart.js
        $chartLabels = $trenMaterial->pluck('nama_material')->toArray();
        $chartData = $trenMaterial->pluck('total_terpasang')->toArray();

        // Kirim event ke frontend agar Chart.js me-render ulang saat dropdown diganti
        $this->dispatch('chart-updated', labels: $chartLabels, data: $chartData);

        return view('livewire.pelaksanaan.dashboard.pelaksanaan-dashboard', [
            'listPenugasan'     => $listPenugasan,
            'totalLaporan'      => $totalLaporan,
            'totalPermintaan'   => $totalPermintaan,
            'laporanTerbaru'    => $laporanTerbaru,
            'chartLabels'       => $chartLabels,
            'chartData'         => $chartData,
        ]);
    }
}