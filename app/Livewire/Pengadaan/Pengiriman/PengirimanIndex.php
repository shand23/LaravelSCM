<?php

namespace App\Livewire\Pengadaan\Pengiriman;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Pengiriman;
use App\Models\Kontrak;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PengirimanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $selected_id;

    // Form Fields
    public $id_kontrak;
    public $tanggal_berangkat;
    public $estimasi_tanggal_tiba;
    public $nama_supir;
    public $plat_kendaraan;
    
    // Properti untuk batas minimal tanggal (Frontend Validation)
    public $min_tanggal_berangkat;

    // Hook: Berjalan otomatis setiap kali dropdown "Pilih PO (Kontrak)" berubah
    public function updatedIdKontrak($value)
    {
        if ($value) {
            $kontrak = Kontrak::find($value);
            // Batas minimal tanggal berangkat adalah tanggal kontrak disepakati
            $this->min_tanggal_berangkat = $kontrak ? $kontrak->tanggal_kontrak : null;
            
            // Jika tgl berangkat yang sudah terisi ternyata sebelum tgl kontrak, reset ke tgl kontrak
            if ($this->tanggal_berangkat && $this->tanggal_berangkat < $this->min_tanggal_berangkat) {
                $this->tanggal_berangkat = $this->min_tanggal_berangkat;
            }
            // Update juga estimasi tiba agar tidak membingungkan
            if ($this->estimasi_tanggal_tiba && $this->estimasi_tanggal_tiba < $this->tanggal_berangkat) {
                $this->estimasi_tanggal_tiba = $this->tanggal_berangkat;
            }
        } else {
            $this->min_tanggal_berangkat = null;
        }
    }

    // Hook: Berjalan otomatis saat tanggal berangkat diubah manual oleh user
    public function updatedTanggalBerangkat($value)
    {
        if ($this->estimasi_tanggal_tiba && $this->estimasi_tanggal_tiba < $value) {
            $this->estimasi_tanggal_tiba = $value;
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $pengiriman = Pengiriman::findOrFail($id);
        $this->selected_id = $id;
        $this->id_kontrak = $pengiriman->id_kontrak;
        $this->tanggal_berangkat = $pengiriman->tanggal_berangkat;
        $this->estimasi_tanggal_tiba = $pengiriman->estimasi_tanggal_tiba;
        $this->nama_supir = $pengiriman->nama_supir;
        $this->plat_kendaraan = $pengiriman->plat_kendaraan;

        // Trigger update min date untuk form edit
        $this->updatedIdKontrak($this->id_kontrak);

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validateData();

        Pengiriman::create([
            'id_kontrak' => $this->id_kontrak,
            'id_user_pengadaan' => Auth::id() ?? 'USR0001', // Sesuaikan ID default ini
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'estimasi_tanggal_tiba' => $this->estimasi_tanggal_tiba,
            'nama_supir' => $this->nama_supir,
            'plat_kendaraan' => $this->plat_kendaraan,
            'status_pengiriman' => 'Pending',
        ]);

        session()->flash('message', 'Jadwal Pengiriman Berhasil Dibuat!');
        $this->closeModal();
    }

    public function update()
    {
        $this->validateData();

        $pengiriman = Pengiriman::findOrFail($this->selected_id);
        $pengiriman->update([
            'id_kontrak' => $this->id_kontrak,
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'estimasi_tanggal_tiba' => $this->estimasi_tanggal_tiba,
            'nama_supir' => $this->nama_supir,
            'plat_kendaraan' => $this->plat_kendaraan,
        ]);

        session()->flash('message', 'Data Pengiriman Berhasil Diperbarui!');
        $this->closeModal();
    }

    public function delete($id)
    {
        $pengiriman = Pengiriman::findOrFail($id);
        $pengiriman->delete();
        session()->flash('message', 'Data Pengiriman Berhasil Dihapus!');
    }

    public function markAsInTransit($id)
    {
        // 1. Cari data pengirimannya
        $pengiriman = Pengiriman::findOrFail($id);
        
        // 2. Update status pengiriman di tabel pengiriman menjadi 'Dalam Perjalanan'
        $pengiriman->update(['status_pengiriman' => 'Dalam Perjalanan']);

        // 3. Update kolom status_pengiriman di tabel Kontrak menjadi 'Pengiriman' (PERBAIKAN DI SINI)
        if ($pengiriman->kontrak) {
            $pengiriman->kontrak->update([
                'status_pengiriman' => 'Pengiriman'
            ]);
        }

        // 4. Berikan notifikasi sukses ganda
        session()->flash('message', 'Truk Dalam Perjalanan & Status Pengiriman di PO otomatis diupdate!');
    }

    private function validateData()
    {
        // Ambil data kontrak untuk validasi backend (mencegah bypass)
        $kontrak = Kontrak::find($this->id_kontrak);
        $tgl_kontrak = $kontrak ? $kontrak->tanggal_kontrak : null;

        $this->validate([
            'id_kontrak' => 'required',
            'tanggal_berangkat' => 'required|date' . ($tgl_kontrak ? '|after_or_equal:' . $tgl_kontrak : ''),
            'estimasi_tanggal_tiba' => 'required|date|after_or_equal:tanggal_berangkat',
            'nama_supir' => 'nullable|string|max:100',
            'plat_kendaraan' => 'nullable|string|max:20',
        ], [
            'id_kontrak.required' => 'PO (Kontrak) harus dipilih.',
            'tanggal_berangkat.after_or_equal' => 'Tgl Berangkat tidak boleh sebelum Tgl Kontrak (' . ($tgl_kontrak ? Carbon::parse($tgl_kontrak)->format('d M Y') : '-') . ').',
            'estimasi_tanggal_tiba.after_or_equal' => 'Estimasi Tiba tidak boleh sebelum Tgl Berangkat.',
        ]);
    }

    private function resetForm()
    {
        $this->reset(['id_kontrak', 'tanggal_berangkat', 'estimasi_tanggal_tiba', 'nama_supir', 'plat_kendaraan', 'selected_id', 'min_tanggal_berangkat']);
        $this->tanggal_berangkat = date('Y-m-d');
        $this->estimasi_tanggal_tiba = date('Y-m-d', strtotime('+1 day'));
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pengadaan.pengiriman.pengiriman-index', [
            'listPengiriman' => Pengiriman::with(['kontrak', 'userPengadaan'])
                ->where('id_pengiriman', 'like', "%{$this->search}%")
                ->orWhereHas('kontrak', function($q) {
                    $q->where('nomor_kontrak', 'like', "%{$this->search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            // Hanya ambil PO yang status_kontrak-nya Disepakati
            'listKontrak' => Kontrak::where('status_kontrak', 'Disepakati')->orderBy('created_at', 'desc')->get()
        ]);
    }
}