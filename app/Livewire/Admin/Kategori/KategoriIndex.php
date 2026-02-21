<?php

namespace App\Livewire\Admin\Kategori;

use Livewire\Component;
use App\Models\KategoriMaterial;
use Livewire\WithPagination;

class KategoriIndex extends Component
{
    use WithPagination;

    // Variabel Form
    public $id_kategori_material, $nama_kategori, $deskripsi;
    public $status_kategori = 'Aktif';

    // State Modal & Edit
    public $isModalOpen = false;
    public $kategori_id_to_edit = null;

    protected function rules()
    {
        return [
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'status_kategori' => 'required|in:Aktif,Nonaktif',
        ];
    }

    public function render()
    {
        return view('livewire.admin.kategori.kategori-index', [
            'kategori' => KategoriMaterial::latest()->paginate(10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $data = KategoriMaterial::findOrFail($id);
        
        $this->kategori_id_to_edit = $id;
        $this->id_kategori_material = $data->id_kategori_material;
        $this->nama_kategori = $data->nama_kategori;
        $this->deskripsi = $data->deskripsi;
        $this->status_kategori = $data->status_kategori;

        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        KategoriMaterial::updateOrCreate(
            ['id_kategori_material' => $this->kategori_id_to_edit],
            [
                'nama_kategori' => $this->nama_kategori,
                'deskripsi' => $this->deskripsi,
                'status_kategori' => $this->status_kategori,
            ]
        );

        session()->flash('message', $this->kategori_id_to_edit ? 'Kategori diperbarui.' : 'Kategori ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        // Cek apakah kategori dipakai di tabel Material? (Opsional, nanti ditambahkan)
        KategoriMaterial::find($id)->delete();
        session()->flash('message', 'Kategori dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->id_kategori_material = '';
        $this->nama_kategori = '';
        $this->deskripsi = '';
        $this->status_kategori = 'Aktif';
        $this->kategori_id_to_edit = null;
        $this->resetErrorBag();
    }
}