<?php

namespace App\Livewire\Manajemen\Penugasan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PenugasanProyek;
use App\Models\Proyek;
use App\Models\User;
use Carbon\Carbon;

class PenugasanIndex extends Component
{
    use WithPagination;

    // Variabel Form
    public $id_penugasan, $id_user, $id_proyek, $peran_proyek, $tanggal_mulai, $tanggal_selesai, $status_penugasan;
    
    // Variabel Penampung Jadwal Proyek (Untuk Validasi Tanggal)
    public $proyek_tanggal_mulai;
    public $proyek_tanggal_selesai;

    public $isModalOpen = false;
    public $isEditMode = false;

    // Filter, Pencarian, & Sorting
    public $search = '';
    public $filterProyek = '';
    public $filterStatus = '';
    
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProyek() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function sortBy($columnName)
    {
        if ($this->sortColumn === $columnName) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortColumn = $columnName;
        }
        $this->resetPage();
    }

    // AJAIB: Fungsi ini otomatis jalan saat user memilih Proyek di Dropdown
    public function updatedIdProyek($value)
    {
        if ($value) {
            $proyek = Proyek::find($value);
            if ($proyek) {
                $this->proyek_tanggal_mulai = $proyek->tanggal_mulai ? Carbon::parse($proyek->tanggal_mulai)->format('Y-m-d') : null;
                $this->proyek_tanggal_selesai = $proyek->tanggal_selesai ? Carbon::parse($proyek->tanggal_selesai)->format('Y-m-d') : null;
            }
        } else {
            $this->proyek_tanggal_mulai = null;
            $this->proyek_tanggal_selesai = null;
        }
        
        // Reset isian tanggal agar user memilih ulang sesuai batas proyek
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
    }

    protected function rules()
    {
        $rules = [
            'id_user' => 'required',
            'id_proyek' => 'required',
            'peran_proyek' => 'required|string|max:100',
            'status_penugasan' => 'required|string',
        ];

        // Validasi backend: Tanggal mulai penugasan harus di dalam rentang jadwal Proyek
        if ($this->proyek_tanggal_mulai) {
            $rules['tanggal_mulai'] = 'nullable|date|after_or_equal:' . $this->proyek_tanggal_mulai;
            if ($this->proyek_tanggal_selesai) {
                $rules['tanggal_mulai'] .= '|before_or_equal:' . $this->proyek_tanggal_selesai;
            }
        } else {
            $rules['tanggal_mulai'] = 'nullable|date';
        }

        // Validasi backend: Tanggal selesai tidak boleh kurang dari tgl mulai, dan tidak boleh lewat dari tgl proyek selesai
        if ($this->tanggal_mulai) {
             $rules['tanggal_selesai'] = 'nullable|date|after_or_equal:tanggal_mulai';
             if ($this->proyek_tanggal_selesai) {
                 $rules['tanggal_selesai'] .= '|before_or_equal:' . $this->proyek_tanggal_selesai;
             }
        } else {
            $rules['tanggal_selesai'] = 'nullable|date';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh mendahului jadwal proyek.',
            'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh melewati jadwal proyek.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'tanggal_selesai.before_or_equal' => 'Tanggal selesai tidak boleh melewati jadwal proyek.',
        ];
    }

    public function render()
    {
        $query = PenugasanProyek::with(['user', 'proyek']);

        if ($this->search) {
            $query->where('peran_proyek', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($q) {
                      $q->where('nama_lengkap', 'like', '%' . $this->search . '%'); // Pakai nama_lengkap
                  })
                  ->orWhereHas('proyek', function($q) {
                      $q->where('nama_proyek', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->filterProyek) {
            $query->where('id_proyek', $this->filterProyek);
        }

        if ($this->filterStatus) {
            $query->where('status_penugasan', $this->filterStatus);
        }

        $penugasans = $query->orderBy($this->sortColumn, $this->sortDirection)->paginate(10);
        
        $daftarProyek = Proyek::where('status_proyek', '!=', 'Selesai')->get();
        
        // HANYA MENGAMBIL USER DENGAN ROLE 'Tim Pelaksanaan'
        $daftarUser = User::where('ROLE', 'Tim Pelaksanaan')->get(); 

        return view('livewire.manajemen.penugasan.penugasan-index', compact('penugasans', 'daftarProyek', 'daftarUser'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->id_penugasan = $this->generateIdPenugasan();
        $this->status_penugasan = 'Aktif'; 
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        PenugasanProyek::updateOrCreate(
            ['id_penugasan' => $this->id_penugasan],
            [
                'id_user' => $this->id_user,
                'id_proyek' => $this->id_proyek,
                'peran_proyek' => $this->peran_proyek,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'status_penugasan' => $this->status_penugasan,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Data Penugasan berhasil diperbarui.' : 'Penugasan baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $tugas = PenugasanProyek::findOrFail($id);
        
        $this->id_penugasan = $tugas->id_penugasan;
        $this->id_user = $tugas->id_user;
        $this->id_proyek = $tugas->id_proyek;
        $this->peran_proyek = $tugas->peran_proyek;
        
        $this->tanggal_mulai = $tugas->tanggal_mulai ? Carbon::parse($tugas->tanggal_mulai)->format('Y-m-d') : null;
        $this->tanggal_selesai = $tugas->tanggal_selesai ? Carbon::parse($tugas->tanggal_selesai)->format('Y-m-d') : null;
        $this->status_penugasan = $tugas->status_penugasan;

        // Panggil updatedIdProyek agar batas tanggal kalender diset untuk modal Edit
        $this->updatedIdProyek($this->id_proyek);
        
        // Kembalikan value tanggal yang sempat ter-reset oleh fungsi updatedIdProyek di atas
        $this->tanggal_mulai = $tugas->tanggal_mulai ? Carbon::parse($tugas->tanggal_mulai)->format('Y-m-d') : null;
        $this->tanggal_selesai = $tugas->tanggal_selesai ? Carbon::parse($tugas->tanggal_selesai)->format('Y-m-d') : null;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function markAsSelesai($id)
    {
        PenugasanProyek::findOrFail($id)->update(['status_penugasan' => 'Selesai']);
        session()->flash('message', 'Penugasan berhasil diselesaikan.');
    }

    public function delete($id)
    {
        PenugasanProyek::findOrFail($id)->delete();
        session()->flash('message', 'Penugasan berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->id_penugasan = '';
        $this->id_user = '';
        $this->id_proyek = '';
        $this->peran_proyek = '';
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
        $this->status_penugasan = '';
        $this->proyek_tanggal_mulai = null;
        $this->proyek_tanggal_selesai = null;
    }

    private function generateIdPenugasan()
    {
        $lastTugas = PenugasanProyek::orderBy('id_penugasan', 'desc')->first();
        
        if (!$lastTugas) {
            return 'PNG0001';
        }

        $lastId = $lastTugas->id_penugasan;
        $number = (int) substr($lastId, 3);
        $number++; 
        
        return 'PNG' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}