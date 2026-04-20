<?php

namespace App\Livewire\Logistik\Kategori;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KategoriMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout; // <--- Penting untuk layout

#[Layout('layouts.app')] // <--- Menentukan layout agar tidak error
class KategoriIndex extends Component
{
    use WithPagination;

    public $id_kategori_material, $nama_kategori, $deskripsi, $status_kategori;
    public $isModalOpen = false;
    public $isEditMode = false;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        $rules = [
            'deskripsi' => 'nullable|string',
            'status_kategori' => 'required|string',
        ];

        if ($this->isEditMode) {
            $rules['nama_kategori'] = 'required|string|max:100|unique:kategori_material,nama_kategori,' . $this->id_kategori_material . ',id_kategori_material';
        } else {
            $rules['nama_kategori'] = 'required|string|max:100|unique:kategori_material,nama_kategori';
        }

        return $rules;
    }

    /**
     * Fungsi Helper untuk cek izin (Backend Security)
     */
    private function checkPermission()
    {
        if (!Auth::user()->can_manage_master) {
            session()->flash('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
            return false;
        }
        return true;
    }

    public function create()
    {
        if (!$this->checkPermission()) return; // Cek Izin

        $this->resetFields();
        $this->status_kategori = 'Aktif';
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function store()
    {
        if (!$this->checkPermission()) return; // Cek Izin
        $this->validate();

        if ($this->isEditMode) {
            $kategori = KategoriMaterial::findOrFail($this->id_kategori_material);
            $kategori->update([
                'nama_kategori' => $this->nama_kategori,
                'deskripsi' => $this->deskripsi,
                'status_kategori' => $this->status_kategori,
            ]);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            KategoriMaterial::create([
                'nama_kategori' => $this->nama_kategori,
                'deskripsi' => $this->deskripsi,
                'status_kategori' => $this->status_kategori,
            ]);
            session()->flash('message', 'Kategori baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        if (!$this->checkPermission()) return; // Cek Izin

        $kategori = KategoriMaterial::findOrFail($id);
        $this->id_kategori_material = $kategori->id_kategori_material;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->deskripsi = $kategori->deskripsi;
        $this->status_kategori = $kategori->status_kategori;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        if (!$this->checkPermission()) return; // Cek Izin

        KategoriMaterial::findOrFail($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->id_kategori_material = '';
        $this->nama_kategori = '';
        $this->deskripsi = '';
        $this->status_kategori = '';
    }

    public function render()
    {
        $kategoris = KategoriMaterial::where('nama_kategori', 'like', '%' . $this->search . '%')
            ->orderBy('id_kategori_material', 'desc')
            ->paginate(10);

        return view('livewire.logistik.kategori.kategori-index', [
            'kategoris' => $kategoris
        ]);
    }
}