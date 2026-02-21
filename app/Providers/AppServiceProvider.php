<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Proyek; // Import Model Proyek
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mengirim data ke semua view (Header & Sidebar)
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Hitung jumlah proyek yang "Aktif" tapi sudah lewat tanggal selesai
                $notifProyek = Proyek::where('status_proyek', 'Aktif')
                    ->whereNotNull('tanggal_selesai')
                    ->where('tanggal_selesai', '<', now()->format('Y-m-d'))
                    ->count();

                // Anda bisa menambah hitungan notifikasi lain di sini (misal: stok material menipis)
                $totalNotif = $notifProyek; 

                $view->with('jmlNotif', $totalNotif);
            } else {
                $view->with('jmlNotif', 0);
            }
        });
    }
}