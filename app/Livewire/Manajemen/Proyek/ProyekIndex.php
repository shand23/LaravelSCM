<?php

namespace App\Livewire\Manajemen\Proyek;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proyek;
use Carbon\Carbon;

class ProyekIndex extends Component
{
    use WithPagination;

    // Variabel Form
    public $id_proyek, $nama_proyek, $lokasi_proyek, $tanggal_mulai, $tanggal_selesai, $deskripsi_proyek, $status_proyek;
    public $isModalOpen = false;
    public $isEditMode = false;

    // Variabel Pencarian & Filter
    public $search = '';
    public $filterBulan = '';
    public $filterTahun = '';

    // Variabel Sorting
    public $sortColumn = 'created_at'; // Default kolom urutan
    public $sortDirection = 'desc'; // Default arah urutan (Terbaru)

    // Reset halaman ke 1 setiap kali user melakukan sesuatu
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterBulan() { $this->resetPage(); }
    public function updatingFilterTahun() { $this->resetPage(); }

    // Fungsi untuk mengatur Sorting Kolom
    public function sortBy($columnName)
    {
        if ($this->sortColumn === $columnName) {
            // Jika kolom yang sama diklik lagi, balikkan arahnya (asc -> desc atau desc -> asc)
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Jika kolom baru yang diklik, set arah ke asc
            $this->sortDirection = 'asc';
            $this->sortColumn = $columnName;
        }
        $this->resetPage();
    }

    protected function rules()
    {
        $rules = [
            'nama_proyek' => 'required|string|max:150',
            'lokasi_proyek' => 'nullable|string|max:150',
            'deskripsi_proyek' => 'nullable|string',
            'status_proyek' => 'required|in:Aktif,Selesai,Ditunda',
        ];

        if (!$this->isEditMode) {
            $rules['tanggal_mulai'] = 'nullable|date|after_or_equal:today';
        } else {
            $rules['tanggal_mulai'] = 'nullable|date';
        }

        $rules['tanggal_selesai'] = 'nullable|date|after_or_equal:tanggal_mulai';

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama_proyek.required' => 'Nama proyek wajib diisi.',
            'status_proyek.required' => 'Status proyek wajib dipilih.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ];
    }

    public function render()
    {
        $query = Proyek::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_proyek', 'like', '%' . $this->search . '%')
                  ->orWhere('id_proyek', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterBulan) {
            $query->whereMonth('tanggal_mulai', $this->filterBulan);
        }

        if ($this->filterTahun) {
            $query->whereYear('tanggal_mulai', $this->filterTahun);
        }

        // Terapkan fungsi orderBy sesuai dengan kolom dan arah urutan
        $proyeks = $query->orderBy($this->sortColumn, $this->sortDirection)->paginate(10);
        
        $overdueCount = Proyek::where('status_proyek', '!=', 'Selesai')
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_selesai', '<', Carbon::today())
            ->count();
        
        return view('livewire.manajemen.proyek.proyek-index', compact('proyeks', 'overdueCount'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->id_proyek = $this->generateIdProyek(); 
        $this->status_proyek = 'Aktif'; 
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        Proyek::updateOrCreate(
            ['id_proyek' => $this->id_proyek],
            [
                'nama_proyek' => $this->nama_proyek,
                'lokasi_proyek' => $this->lokasi_proyek,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'deskripsi_proyek' => $this->deskripsi_proyek,
                'status_proyek' => $this->status_proyek,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Data Proyek berhasil diperbarui.' : 'Proyek baru berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        
        $this->id_proyek = $proyek->id_proyek;
        $this->nama_proyek = $proyek->nama_proyek;
        $this->lokasi_proyek = $proyek->lokasi_proyek;
        
        $this->tanggal_mulai = $proyek->tanggal_mulai ? Carbon::parse($proyek->tanggal_mulai)->format('Y-m-d') : null;
        $this->tanggal_selesai = $proyek->tanggal_selesai ? Carbon::parse($proyek->tanggal_selesai)->format('Y-m-d') : null;
        
        $this->deskripsi_proyek = $proyek->deskripsi_proyek;
        $this->status_proyek = $proyek->status_proyek;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function markAsSelesai($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->update(['status_proyek' => 'Selesai']);
        
        session()->flash('message', 'Hore! Proyek ' . $proyek->nama_proyek . ' berhasil diselesaikan.');
    }

    public function delete($id)
    {
        Proyek::findOrFail($id)->delete();
        session()->flash('message', 'Data Proyek berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->id_proyek = '';
        $this->nama_proyek = '';
        $this->lokasi_proyek = '';
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
        $this->deskripsi_proyek = '';
        $this->status_proyek = '';
    }

    private function generateIdProyek()
    {
        $lastProyek = Proyek::orderBy('id_proyek', 'desc')->first();
        
        if (!$lastProyek) {
            return 'PRY0001';
        }

        $lastId = $lastProyek->id_proyek;
        $number = (int) substr($lastId, 3);
        $number++; 
        
        return 'PRY' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}