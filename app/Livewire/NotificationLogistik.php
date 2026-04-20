<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationLogistik extends Component
{
    public $lastNotifCount = 0;

    public function markAsRead($id, $type)
    {
        $readNotifs = session()->get('read_notifs', []);
        
        // Simpan ID unik (misal: 'pengiriman-DO123' atau 'permintaan-REQ456')
        $notifKey = $type . '-' . $id;

        if (!in_array($notifKey, $readNotifs)) {
            $readNotifs[] = $notifKey;
            session()->put('read_notifs', $readNotifs);
        }

        // Logika Redirect berdasarkan tipe
        if ($type === 'pengiriman') {
            $pengiriman = Pengiriman::find($id);
            if ($pengiriman && $pengiriman->status_pengiriman === 'Tiba di Lokasi') {
                return redirect()->to('/logistik/penerimaan?id_pengiriman=' . $id);
            }
            return redirect()->to('/logistik/penerimaan');
        } 
        
        if ($type === 'permintaan') {
            // Langsung ke halaman permintaan proyek sesuai rute Anda
            return redirect()->to('/logistik/permintaan-proyek?id_permintaan=' . $id);
        }
    }

    public function render()
{
    if (Auth::user()->ROLE !== 'Logistik') {
        return view('livewire.notification-logistik', ['notifikasi' => collect(), 'jmlNotifUnread' => 0]);
    }

    $readNotifs = session()->get('read_notifs', []);

    // 1. Ambil Notifikasi Pengiriman
    $notifPengiriman = Pengiriman::whereIn('status_pengiriman', ['Tiba di Lokasi', 'Dalam Perjalanan'])
        ->get()
        ->map(function($item) use ($readNotifs) {
            $item->type = 'pengiriman';
            $item->notif_id = $item->id_pengiriman;
            $item->is_read = in_array('pengiriman-' . $item->id_pengiriman, $readNotifs);
            $item->label = ($item->status_pengiriman == 'Tiba di Lokasi') ? 'Truk Tiba' : 'Dalam Perjalanan';
            $item->color = ($item->status_pengiriman == 'Tiba di Lokasi') ? 'text-indigo-600' : 'text-amber-600';
            $item->desc = "ID: " . $item->id_pengiriman;
            return $item;
        });

    // 2. Ambil Notifikasi Permintaan Proyek
    $notifPermintaan = DB::table('permintaan_proyek')
        ->where('status_permintaan', 'Disetujui PM')
        ->get()
        ->map(function($item) use ($readNotifs) {
            $item->type = 'permintaan';
            $item->notif_id = $item->id_permintaan; 
            $item->is_read = in_array('permintaan-' . $item->id_permintaan, $readNotifs);
            $item->label = 'Permintaan Baru';
            $item->color = 'text-green-600';
            $item->desc = "ID: " . $item->id_permintaan . " (Disetujui PM)";
            $item->updated_at = $item->updated_at; 
            return $item;
        });

    // 3. Gabungkan, Filter yang belum dibaca, dan Urutkan
    $allNotif = $notifPengiriman->concat($notifPermintaan)
        ->where('is_read', false)
        ->sortByDesc('updated_at');

    $jmlNotifUnread = $allNotif->count();

    // --- PERUBAHAN DISINI: Menggunakan Session agar suara tidak rewel ---
    $lastCountInSession = session()->get('last_notif_count', 0);

    if ($jmlNotifUnread > $lastCountInSession) {
        $this->dispatch('play-notif-sound');
    }

    // Update jumlah terakhir ke session
    session()->put('last_notif_count', $jmlNotifUnread);
    // ------------------------------------------------------------------

    return view('livewire.notification-logistik', [
        'notifikasi' => $allNotif,
        'jmlNotifUnread' => $jmlNotifUnread
    ]);
}
}