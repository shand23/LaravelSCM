<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationManajer extends Component
{
   public function markAsRead($id)
    {
        // Update database: Tandai sudah dibaca oleh Top Manajemen / Manajer
        DB::table('permintaan_proyek')
            ->where('id_permintaan', $id)
            ->update(['is_read_manajer' => true]);

        // Redirect ke halaman permintaan menggunakan nama route yang ada di web.php
       
        return redirect()->route('manajemen.approval');
        // Catatan: Jika Anda lebih suka menggunakan URL langsung, Anda bisa memakai kode di bawah ini:
        // return redirect()->to('/pelaksanaan/permintaan');
    }

    public function render()
    {
        if (Auth::user()->ROLE !== 'Top Manajemen') {
            return view('livewire.notification-manajer', ['notifikasi' => collect(), 'jmlNotifUnread' => 0]);
        }

        // Ambil permintaan dengan status 'Menunggu Persetujuan' yang belum dibaca manajer
        $notifikasi = DB::table('permintaan_proyek')
            ->where('status_permintaan', 'Menunggu Persetujuan')
            ->where('is_read_manajer', false)
            ->get()
            ->map(function($item) {
                $item->notif_id = $item->id_permintaan;
                $item->label = 'Persetujuan Baru';
                $item->color = 'text-amber-600';
                $item->desc = "Permintaan ID: " . $item->id_permintaan . " menunggu persetujuan Anda.";
                return $item;
            });

        $jmlNotifUnread = $notifikasi->count();

        // --- LOGIKA SUARA (Mencegah bunyi saat login/refresh) ---
        $isFirstLoad = !session()->has('last_manajer_notif_states');
        $lastStates = session()->get('last_manajer_notif_states', []);
        $shouldPlaySound = false;

        if (!$isFirstLoad) {
            foreach ($notifikasi as $notif) {
                $key = 'permintaan-' . $notif->notif_id;
                // Bunyi jika ada ID baru yang masuk ke list 'Menunggu Persetujuan'
                if (!isset($lastStates[$key])) {
                    $shouldPlaySound = true;
                    break;
                }
            }
        }

        if ($shouldPlaySound && $notifikasi->isNotEmpty()) {
            $this->dispatch('play-notif-sound');
        }

        // Simpan state terbaru ke session
        $newStates = [];
        foreach ($notifikasi as $notif) {
            $newStates['permintaan-' . $notif->notif_id] = 'Menunggu Persetujuan';
        }
        session()->put('last_manajer_notif_states', $newStates);

        return view('livewire.notification-manajer', [
            'notifikasi' => $notifikasi,
            'jmlNotifUnread' => $jmlNotifUnread
        ]);
    }
}