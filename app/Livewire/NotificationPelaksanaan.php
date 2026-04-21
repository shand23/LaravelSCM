<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationPelaksanaan extends Component
{
    public function markAsRead($id)
    {
        // Update database: Tandai sudah dibaca oleh Tim Pelaksanaan
        DB::table('permintaan_proyek')
            ->where('id_permintaan', $id)
            ->update(['is_read_pelaksanaan' => true]);

        // Redirect ke halaman permintaan milik Pelaksana
        // Pastikan nama route ini sesuai dengan yang ada di web.php Anda
        return redirect()->route('pelaksanaan.permintaan');
    }

    public function render()
    {
        // Keamanan: Pastikan hanya Tim Pelaksanaan yang memproses data ini
        if (Auth::user()->ROLE !== 'Tim Pelaksanaan') {
            return view('livewire.notification-pelaksanaan', [
                'notifikasi' => collect(), 
                'jmlNotifUnread' => 0
            ]);
        }

        // Status yang dipantau untuk Pelaksana
        $statusTarget = ['Disetujui PM', 'Ditolak', 'Diproses Sebagian', 'Selesai'];

        // Ambil data notifikasi yang belum dibaca HANYA UNTUK PEMBUAT DATA
        $notifikasi = DB::table('permintaan_proyek')
            ->where('id_user', Auth::user()->id_user ?? Auth::id()) // <--- FILTER PERSONAL
            ->whereIn('status_permintaan', $statusTarget)
            ->where('is_read_pelaksanaan', false)
            ->get()
            ->map(function($item) {
                $item->notif_id = $item->id_permintaan;
                
                // Penentuan Label, Warna, dan Deskripsi secara dinamis
                switch ($item->status_permintaan) {
                    case 'Disetujui PM':
                        $item->label = 'Disetujui PM';
                        $item->color = 'text-green-600';
                        $item->desc = "Permintaan ID: {$item->id_permintaan} disetujui.";
                        break;
                    case 'Ditolak':
                        $item->label = 'Ditolak';
                        $item->color = 'text-red-600';
                        $item->desc = "Permintaan ID: {$item->id_permintaan} perlu direvisi.";
                        break;
                    case 'Diproses Sebagian':
                        $item->label = 'Proses Logistik';
                        $item->color = 'text-amber-600';
                        $item->desc = "Permintaan ID: {$item->id_permintaan} diproses sebagian.";
                        break;
                    case 'Selesai':
                        $item->label = 'Selesai';
                        $item->color = 'text-indigo-600';
                        $item->desc = "Permintaan ID: {$item->id_permintaan} sudah dipenuhi.";
                        break;
                }
                return $item;
            });

        $jmlNotifUnread = $notifikasi->count();

        // --- LOGIKA SUARA CERDAS (Sesuai Logistik & Manajer) ---
        $isFirstLoad = !session()->has('last_pelaksana_states');
        $lastStates = session()->get('last_pelaksana_states', []);
        $shouldPlaySound = false;

        if (!$isFirstLoad) {
            foreach ($notifikasi as $notif) {
                $key = 'permintaan-' . $notif->notif_id;
                // Bunyi jika ada ID baru atau statusnya bergeser (misal dari Disetujui ke Selesai)
                if (!isset($lastStates[$key]) || $lastStates[$key] !== $notif->status_permintaan) {
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
            $newStates['permintaan-' . $notif->notif_id] = $notif->status_permintaan;
        }
        session()->put('last_pelaksana_states', $newStates);

        return view('livewire.notification-pelaksanaan', [
            'notifikasi' => $notifikasi,
            'jmlNotifUnread' => $jmlNotifUnread
        ]);
    }
}