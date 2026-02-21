<?php

namespace App\Livewire\Admin\Proyek;

use App\Models\PenugasanProyek;
use App\Models\User;
use App\Models\Proyek;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon; // Wajib import Carbon

class PenugasanIndex extends Component
{
    use WithPagination;

    // --- PROPERTI DATABASE ---
    public $id_penugasan, $id_user, $id_proyek, $peran_proyek, $tanggal_mulai, $tanggal_selesai;
    public $status_penugasan = 'Aktif';

    // --- PROPERTI UI & LOGIC ---
    public $isModalOpen = false;
    public $isEditMode = false;
    public $search = '';

    // --- PROPERTI BATASAN TANGGAL (Untuk HTML min/max) ---
    public $minDate = null;
    public $maxDate = null;

    // Reset halaman saat searching
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. Ambil Data Penugasan (Join User & Proyek)
        $penugasans = PenugasanProyek::with(['user', 'proyek'])
            ->whereHas('user', function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('proyek', function($q) {
                $q->where('nama_proyek', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        // 2. Ambil List User (Hanya Role Tim Proyek)
        $users = User::where('role', 'Tim Proyek')->get();

        // 3. Ambil List Proyek (Hanya yang Aktif)
        $proyeks = Proyek::where('status_proyek', 'Aktif')->get();

        return view('livewire.admin.proyek.penugasan-index', [
            'penugasans' => $penugasans,
            'users' => $users,
            'proyeks' => $proyeks
        ])->layout('layouts.app');
    }

    // --- MODAL & RESET ---
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->id_penugasan = null;
        $this->id_user = '';
        $this->id_proyek = '';
        $this->peran_proyek = '';
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
        $this->status_penugasan = 'Aktif';
        $this->isEditMode = false;
        
        // Reset batasan tanggal juga
        $this->minDate = null;
        $this->maxDate = null;
    }

    // --- LOGIC GANTI PROYEK (LIFECYCLE HOOK) ---
    // Jalan otomatis saat dropdown proyek berubah
    public function updatedIdProyek($value)
    {
        if ($value) {
            $proyek = Proyek::find($value);
            if ($proyek) {
                // Format tanggal harus Y-m-d agar terbaca oleh input type="date" HTML
                $this->minDate = Carbon::parse($proyek->tanggal_mulai)->format('Y-m-d');
                $this->maxDate = Carbon::parse($proyek->tanggal_selesai)->format('Y-m-d');
                
                // Reset inputan user jika bukan mode edit
                if(!$this->isEditMode) {
                    $this->tanggal_mulai = null;
                    $this->tanggal_selesai = null;
                }
            }
        } else {
            $this->minDate = null;
            $this->maxDate = null;
        }
    }

    // --- SIMPAN DATA (CREATE / UPDATE) ---
    public function store()
    {
        // 1. Ambil Data Proyek untuk Validasi Server
        $proyek = Proyek::find($this->id_proyek);
        
        // Pastikan format Y-m-d untuk validasi
        $tglMulaiProyek = $proyek ? Carbon::parse($proyek->tanggal_mulai)->format('Y-m-d') : null;
        $tglSelesaiProyek = $proyek ? Carbon::parse($proyek->tanggal_selesai)->format('Y-m-d') : null;

        // 2. Validasi
        $this->validate([
            'id_user' => 'required',
            'id_proyek' => 'required',
            'peran_proyek' => 'required|string',
            
            // Validasi Tanggal Server Side (Double Protection)
            'tanggal_mulai' => [
                'required', 
                'date', 
                'after_or_equal:' . $tglMulaiProyek
            ],
            'tanggal_selesai' => [
                'nullable', 
                'date', 
                'after_or_equal:tanggal_mulai', 
                'before_or_equal:' . $tglSelesaiProyek
            ],
        ], [
            // Custom Pesan Error
            'tanggal_mulai.after_or_equal' => 'Tanggal tugas tidak boleh sebelum mulai proyek (' . $tglMulaiProyek . ').',
            'tanggal_selesai.before_or_equal' => 'Tanggal tugas tidak boleh melebihi selesai proyek (' . $tglSelesaiProyek . ').',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai tugas.',
        ]);

        // 3. Generate ID (Jika Baru)
        if (!$this->isEditMode) {
            $count = PenugasanProyek::count() + 1;
            // Format: ASN-20240216-001
            $this->id_penugasan = 'ASN-' . date('Ymd') . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        // 4. Simpan ke Database
        PenugasanProyek::updateOrCreate(
            ['id_penugasan' => $this->id_penugasan],
            [
                'id_user' => $this->id_user,
                'id_proyek' => $this->id_proyek,
                'peran_proyek' => $this->peran_proyek,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'status_penugasan' => $this->status_penugasan,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Penugasan diperbarui.' : 'Penugasan berhasil dibuat.');
        $this->closeModal();
    }

    // --- EDIT DATA ---
    public function edit($id)
    {
        $p = PenugasanProyek::findOrFail($id);
        
        $this->isEditMode = true;
        
        $this->id_penugasan = $p->id_penugasan;
        $this->id_user = $p->id_user;
        $this->id_proyek = $p->id_proyek;
        
        // PENTING: Set min/max date berdasarkan proyek yang sedang diedit
        $this->updatedIdProyek($p->id_proyek);

        $this->peran_proyek = $p->peran_proyek;
        $this->tanggal_mulai = $p->tanggal_mulai;
        $this->tanggal_selesai = $p->tanggal_selesai;
        $this->status_penugasan = $p->status_penugasan;

        $this->openModal();
    }

    // --- HAPUS DATA (HANYA NONAKTIF) ---
    public function delete($id)
    {
        $penugasan = PenugasanProyek::find($id);

        if ($penugasan) {
            // Cek Status: Hanya boleh hapus jika Nonaktif
            if ($penugasan->status_penugasan == 'Aktif') {
                session()->flash('error', 'Gagal: Penugasan Aktif tidak bisa dihapus. Nonaktifkan terlebih dahulu!');
                return;
            }

            $penugasan->delete();
            session()->flash('message', 'Data penugasan berhasil dihapus.');
        }
    }
}