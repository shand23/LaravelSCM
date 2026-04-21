<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationPengadaan extends Component
{
    public function markAsRead($id, $type)
    {
        // Logika Redirect dan Update DB berdasarkan tipe
        if ($type === 'pengajuan') {
            DB::table('pengajuan_pembelian')
                ->where('id_pengajuan', $id)
                ->update(['is_read_pengadaan' => true]);
            
            // Redirect ke halaman Proses RFQ (Pesanan) berdasarkan web.php
            return redirect()->route('pengadaan.pesanan');
        } 
        
        if ($type === 'pengiriman') {
            DB::table('pengiriman')
                ->where('id_pengiriman', $id)
                ->update(['is_read_pengadaan' => true]);
            
            // Redirect ke halaman Pengiriman berdasarkan web.php
            return redirect()->route('pengadaan.pengiriman');
        }
    }

    public function render()
    {
        if (Auth::user()->ROLE !== 'Tim Pengadaan') {
            return view('livewire.notification-pengadaan', ['notifikasi' => collect(), 'jmlNotifUnread' => 0]);
        }

        // 1. Ambil Notifikasi Pengajuan Pembelian (Dari DB: is_read_pengadaan = false)
        $notifPengajuan = DB::table('pengajuan_pembelian')
            ->where('status_pengajuan', 'Menunggu Pengadaan')
            ->where('is_read_pengadaan', false)
            ->get()
            ->map(function($item) {
                $item->type = 'pengajuan';
                $item->notif_id = $item->id_pengajuan;
                $item->label = 'Pengajuan Baru';
                $item->color = 'text-blue-600';
                $item->desc = "ID: " . $item->id_pengajuan . " (Menunggu RFQ)";
                $item->updated_at = $item->created_at ?? now(); // Fallback waktu
                return $item;
            });

        // 2. Ambil Notifikasi Pengiriman (Dari DB: is_read_pengadaan = false)
        $notifPengiriman = DB::table('pengiriman')
            ->whereIn('status_pengiriman', ['Return & Kirim Ulang', 'Selesai'])
            ->where('is_read_pengadaan', false)
            ->get()
            ->map(function($item) {
                $item->type = 'pengiriman';
                $item->notif_id = $item->id_pengiriman;
                $item->label = ($item->status_pengiriman == 'Return & Kirim Ulang') ? 'Barang Return' : 'Diterima Penuh';
                $item->color = ($item->status_pengiriman == 'Return & Kirim Ulang') ? 'text-red-600' : 'text-green-600';
                $item->desc = "ID: " . $item->id_pengiriman . " (" . $item->status_pengiriman . ")";
                $item->updated_at = $item->updated_at ?? now(); // Fallback waktu
                return $item;
            });

        // 3. Gabungkan dan Urutkan
        $allNotif = $notifPengajuan->concat($notifPengiriman)
            ->sortByDesc('updated_at');

        $jmlNotifUnread = $allNotif->count();

        // 4. --- LOGIKA SUARA CERDAS (Pendeteksi Perubahan Status) ---
        $isFirstLoad = !session()->has('last_pengadaan_states');
        $lastStates = session()->get('last_pengadaan_states', []);
        $shouldPlaySound = false;

        // Jika BUKAN pertama kali load (user sedang aktif), baru kita cek apakah ada yang baru/berubah
        if (!$isFirstLoad) {
            foreach ($allNotif as $notif) {
                $key = $notif->type . '-' . $notif->notif_id;
                $currentStatus = ($notif->type == 'pengiriman') ? $notif->status_pengiriman : 'Menunggu Pengadaan';

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
            $newStates[$key] = ($notif->type == 'pengiriman') ? $notif->status_pengiriman : 'Menunggu Pengadaan';
        }
        session()->put('last_pengadaan_states', $newStates);
        // -------------------------------------------------------------

        return view('livewire.notification-pengadaan', [
            'notifikasi' => $allNotif,
            'jmlNotifUnread' => $jmlNotifUnread
        ]);
    }
}