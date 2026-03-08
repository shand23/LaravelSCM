<?php

namespace App\Livewire\Logistik\Kategori;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KategoriMaterial;

class KategoriIndex extends Component
{
    use WithPagination;

    // Sesuaikan dengan field di Model
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

        // Validasi Unique Nama Kategori (Abaikan ID yang sedang diedit)
        if ($this->isEditMode) {
            $rules['nama_kategori'] = 'required|string|max:100|unique:kategori_material,nama_kategori,' . $this->id_kategori_material . ',id_kategori_material';
        } else {
            $rules['nama_kategori'] = 'required|string|max:100|unique:kategori_material,nama_kategori';
        }

        return $rules;
    }

    public function render()
    {
        $kategoris = KategoriMaterial::where('nama_kategori', 'like', '%' . $this->search . '%')
            ->orderBy('id_kategori_material', 'desc')
            ->paginate(10);

        return view('livewire.logistik.kategori.kategori-index', compact('kategoris'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->status_kategori = 'Aktif'; // Set default status
        $this->isModalOpen = true;
    }

    public function store()
    {
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
            // Create baru (ID akan dibuat otomatis oleh Model Boot)
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
}