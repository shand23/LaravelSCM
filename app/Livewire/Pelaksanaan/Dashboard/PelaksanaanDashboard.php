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
    public $selectedProyek = ''; // Variabel untuk menyimpan filter dropdown proyek

    public function render()
    {
        // 1. Ambil ID User yang sedang login
        $userId = Auth::id() ?? 'USR001';

        // 2. Ambil DAFTAR PENUGASAN (untuk isi Dropdown Filter)
        $listPenugasan = PenugasanProyek::with('proyek')
                            ->where('id_user', $userId)
                            ->where('status_penugasan', 'Aktif')
                            ->get();

        // 3. Tentukan target ID Proyek yang akan dihitung 
        if ($this->selectedProyek != '') {
            $proyekTarget = [$this->selectedProyek];
        } else {
            $proyekTarget = $listPenugasan->pluck('id_proyek')->toArray();
        }

        // 4. STATISTIK CEPAT
        
        // Count: Laporan yang sudah dikirim
        $totalLaporan = PenggunaanMaterial::whereIn('id_proyek', $proyekTarget)
                            ->where('id_user_pelaksana', $userId)
                            ->count();

        // Count: Total permintaan barang
        $totalPermintaan = PermintaanProyek::whereIn('id_proyek', $proyekTarget)
                            ->where('id_user', $userId)
                            ->count();

        // Count: Tugas Belum Lapor (Permintaan 'Selesai' tapi belum ada di tabel penggunaan_material)
        $tugasBelumSelesai = PermintaanProyek::whereIn('id_proyek', $proyekTarget)
                            ->where('id_user', $userId)
                            ->where('status_permintaan', 'Selesai')
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('penggunaan_material')
                                    ->whereColumn('penggunaan_material.id_permintaan', 'permintaan_proyek.id_permintaan');
                            })
                            ->count();

        // 5. DATA TABEL (Riwayat Laporan Terbaru - Variabel yang tadi error)
        $laporanTerbaru = PenggunaanMaterial::with('proyek')
                            ->whereIn('id_proyek', $proyekTarget)
                            ->where('id_user_pelaksana', $userId)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        // 6. QUERY UNTUK CHART (Tren 5 Material Terbanyak)
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

        // Siapkan data untuk dikirim ke Chart.js
        $chartLabels = $trenMaterial->pluck('nama_material')->toArray();
        $chartData = $trenMaterial->pluck('total_terpasang')->toArray();

        // Dispatch event untuk update Chart (Livewire 3)
        $this->dispatch('chart-updated', labels: $chartLabels, data: $chartData);

        return view('livewire.pelaksanaan.dashboard.pelaksanaan-dashboard', [
            'listPenugasan'     => $listPenugasan,
            'totalLaporan'      => $totalLaporan,
            'totalPermintaan'   => $totalPermintaan,
            'tugasBelumSelesai' => $tugasBelumSelesai,
            'laporanTerbaru'    => $laporanTerbaru,
            'chartLabels'       => $chartLabels,
            'chartData'         => $chartData,
        ]);
    }
}