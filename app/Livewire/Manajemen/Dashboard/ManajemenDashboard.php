<?php

namespace App\Livewire\Manajemen\Dashboard;

use Livewire\Component;
use App\Models\Proyek;
use App\Models\PenugasanProyek;
use App\Models\PermintaanProyek;

class ManajemenDashboard extends Component
{
    public function mount()
    {
        // Pastikan atribut role sesuai dengan tabel users Anda (ROLE atau role)
        if (auth()->user()->ROLE !== 'Top Manajemen') {
            abort(403);
        }
    }

    public function render()
    {
        // 1. STATISTIK CEPAT
        $totalProyek = Proyek::count();
        $proyekAktif = Proyek::where('status_proyek', 'Aktif')->count();
        
        // Jumlah penugasan aktif
        $timAktif = PenugasanProyek::where('status_penugasan', 'Aktif')->count();
        
        // Jumlah permintaan yang butuh approval
        $menungguApproval = PermintaanProyek::where('status_permintaan', 'Menunggu Persetujuan')->count();

        // 2. DATA TABEL TERBARU (Masing-masing ambil 5 data terakhir)
        $proyekTerbaru = Proyek::latest('created_at')->take(5)->get();
        
        // Memuat relasi proyek dan user (pastikan model PermintaanProyek memiliki relasi belongsTo ke 'proyek' dan 'user')
        $permintaanPending = PermintaanProyek::with(['proyek', 'user']) 
            ->where('status_permintaan', 'Menunggu Persetujuan')
            ->latest('tanggal_permintaan')
            ->take(5)
            ->get();

        return view('livewire.manajemen.dashboard.manajemen-dashboard', [
            'totalProyek'      => $totalProyek,
            'proyekAktif'      => $proyekAktif,
            'timAktif'         => $timAktif,
            'menungguApproval' => $menungguApproval,
            'proyekTerbaru'    => $proyekTerbaru,
            'permintaanPending'=> $permintaanPending,
        ]);
    }
}