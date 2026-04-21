<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationLogistik extends Component
{
    public function markAsRead($id, $type)
    {
        // Logika Redirect dan Update DB berdasarkan tipe
        if ($type === 'pengiriman') {
            Pengiriman::where('id_pengiriman', $id)->update(['is_read_logistik' => true]);
            // Langsung arahkan ke halaman utama penerimaan
            return redirect()->to('/logistik/penerimaan');
        } 
        
        if ($type === 'permintaan') {
            DB::table('permintaan_proyek')->where('id_permintaan', $id)->update(['is_read_logistik' => true]);
            // Langsung arahkan ke halaman utama permintaan
            return redirect()->to('/logistik/permintaan-proyek');
        }
    }

    public function render()
    {
        if (Auth::user()->ROLE !== 'Logistik') {
            return view('livewire.notification-logistik', ['notifikasi' => collect(), 'jmlNotifUnread' => 0]);
        }

        // 1. Ambil Notifikasi Pengiriman (Dari DB: is_read_logistik = false)
        $notifPengiriman = Pengiriman::whereIn('status_pengiriman', ['Tiba di Lokasi', 'Dalam Perjalanan'])
            ->where('is_read_logistik', false)
            ->get()
            ->map(function($item) {
                $item->type = 'pengiriman';
                $item->notif_id = $item->id_pengiriman;
                $item->label = ($item->status_pengiriman == 'Tiba di Lokasi') ? 'Truk Tiba' : 'Dalam Perjalanan';
                $item->color = ($item->status_pengiriman == 'Tiba di Lokasi') ? 'text-indigo-600' : 'text-amber-600';
                $item->desc = "ID: " . $item->id_pengiriman;
                return $item;
            });

        // 2. Ambil Notifikasi Permintaan Proyek (Dari DB: is_read_logistik = false)
        $notifPermintaan = DB::table('permintaan_proyek')
            ->where('status_permintaan', 'Disetujui PM')
            ->where('is_read_logistik', false)
            ->get()
            ->map(function($item) {
                $item->type = 'permintaan';
                $item->notif_id = $item->id_permintaan; 
                $item->label = 'Permintaan Baru';
                $item->color = 'text-green-600';
                $item->desc = "ID: " . $item->id_permintaan . " (Disetujui PM)";
                return $item;
            });

        // 3. Gabungkan dan Urutkan
        $allNotif = $notifPengiriman->concat($notifPermintaan)
            ->sortByDesc('updated_at');

        $jmlNotifUnread = $allNotif->count();

        // 4. --- LOGIKA SUARA CERDAS (Pendeteksi Perubahan Status) ---
       $isFirstLoad = !session()->has('last_notif_states');
        $lastStates = session()->get('last_notif_states', []);
        $shouldPlaySound = false;

        // Jika BUKAN pertama kali load (user sedang aktif), baru kita cek apakah ada yang baru/berubah
        if (!$isFirstLoad) {
            foreach ($allNotif as $notif) {
                $key = $notif->type . '-' . $notif->notif_id;
                $currentStatus = ($notif->type == 'pengiriman') ? $notif->status_pengiriman : 'Disetujui PM';

                // Bunyi jika ada ID baru ATAU statusnya berubah
                if (!isset($lastStates[$key]) || $lastStates[$key] !== $currentStatus) {
                    $shouldPlaySound = true;
                    break;
                }
            }
        }

        if ($shouldPlaySound && $allNotif->isNotEmpty()) {
            $this->dispatch('play-notif-sound');
        }

        // Update peta status ke session
       $newStates = [];
        foreach ($allNotif as $notif) {
            $key = $notif->type . '-' . $notif->notif_id;
            $newStates[$key] = ($notif->type == 'pengiriman') ? $notif->status_pengiriman : 'Disetujui PM';
        }
        session()->put('last_notif_states', $newStates);
        // -------------------------------------------------------------

        return view('livewire.notification-logistik', [
            'notifikasi' => $allNotif,
            'jmlNotifUnread' => $jmlNotifUnread
        ]);
    }
}