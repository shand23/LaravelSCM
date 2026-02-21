<?php

namespace App\Livewire\Admin\Proyek;

use App\Models\Proyek;
use App\Models\PenugasanProyek; // Import model penugasan untuk ambil data anggota
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ProyekIndex extends Component
{
    use WithPagination;

    // --- PROPERTI DATABASE ---
    public $id_proyek, $nama_proyek, $lokasi_proyek, $deskripsi_proyek, $tanggal_mulai, $tanggal_selesai;
    public $status_proyek = 'Aktif';

    // --- PROPERTI UI ---
    public $isModalOpen = false;
    public $isEditMode = false;
    public $search = '';

    // --- PROPERTI MODAL ANGGOTA TIM (BARU) ---
    public $isMemberModalOpen = false;
    public $teamMembers = [];
    public $selectedProyekName = '';

    // Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $proyeks = Proyek::where('nama_proyek', 'like', '%' . $this->search . '%')
            ->orWhere('lokasi_proyek', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.proyek.proyek-index', [
            'proyeks' => $proyeks
        ])->layout('layouts.app');
    }

    // --- CRUD PROYEK ---

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
        $this->id_proyek = null;
        $this->nama_proyek = '';
        $this->lokasi_proyek = '';
        $this->deskripsi_proyek = '';
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
        $this->status_proyek = 'Aktif';
        $this->isEditMode = false;
    }

    public function store()
    {
        $this->validate([
            'nama_proyek' => 'required|string|max:255',
            'lokasi_proyek' => 'required|string|max:255',
            'deskripsi_proyek' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_proyek' => 'required'
        ]);

        // Generate Custom ID jika Baru (Format: PRJ-YYYYMMDD-XXX)
        if (!$this->isEditMode) {
            $count = Proyek::count() + 1;
            $this->id_proyek = 'PRJ-' . date('Ymd') . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        Proyek::updateOrCreate(
            ['id_proyek' => $this->id_proyek],
            [
                'nama_proyek' => $this->nama_proyek,
                'lokasi_proyek' => $this->lokasi_proyek,
                'deskripsi_proyek' => $this->deskripsi_proyek,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'status_proyek' => $this->status_proyek,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Proyek berhasil diperbarui.' : 'Proyek berhasil dibuat.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $p = Proyek::findOrFail($id);
        $this->id_proyek = $p->id_proyek;
        $this->nama_proyek = $p->nama_proyek;
        $this->lokasi_proyek = $p->lokasi_proyek;
        $this->deskripsi_proyek = $p->deskripsi_proyek;
        $this->tanggal_mulai = $p->tanggal_mulai;
        $this->tanggal_selesai = $p->tanggal_selesai;
        $this->status_proyek = $p->status_proyek;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        // Cek apakah ada penugasan terkait sebelum hapus
        $cekPenugasan = PenugasanProyek::where('id_proyek', $id)->exists();

        if($cekPenugasan) {
            session()->flash('error', 'Gagal: Proyek ini memiliki anggota tim aktif. Hapus data penugasan terlebih dahulu.');
        } else {
            Proyek::find($id)->delete();
            session()->flash('message', 'Proyek berhasil dihapus.');
        }
    }

    // --- LOGIC MODAL ANGGOTA TIM ---

    public function showMembers($id)
    {
        $proyek = Proyek::find($id);
        
        if ($proyek) {
            $this->selectedProyekName = $proyek->nama_proyek;
            
            // Ambil data penugasan + data user-nya
            $this->teamMembers = PenugasanProyek::with('user')
                ->where('id_proyek', $id)
                ->orderBy('status_penugasan', 'asc') // Aktif diatas
                ->get();

            $this->isMemberModalOpen = true;
        }
    }

    public function closeMemberModal()
    {
        $this->isMemberModalOpen = false;
        $this->teamMembers = []; // Bersihkan data agar tidak berat
    }
}