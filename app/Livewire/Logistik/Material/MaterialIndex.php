<?php

namespace App\Livewire\Logistik\Material;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Material;
use App\Models\KategoriMaterial;

class MaterialIndex extends Component
{
    use WithPagination;

    // Field form sesuai dengan model Material
    public $id_material, $id_kategori_material, $nama_material, $satuan, $spesifikasi, $standar_kualitas, $status_material;
    
    public $isModalOpen = false;
    public $isEditMode = false;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'id_kategori_material' => 'required|exists:kategori_material,id_kategori_material',
            'nama_material'        => 'required|string|max:150',
            'satuan'               => 'required|string|max:50',
            'spesifikasi'          => 'nullable|string',
            'standar_kualitas'     => 'nullable|string',
            'status_material'      => 'required|in:Aktif,Nonaktif',
        ];
    }

    public function render()
    {
        // Ambil data material beserta relasi kategorinya
        $materials = Material::with('kategori')
            ->where('nama_material', 'like', '%' . $this->search . '%')
            ->orderBy('id_material', 'desc')
            ->paginate(10);

        // Ambil data kategori yang Aktif untuk dropdown form
        $kategoris = KategoriMaterial::where('status_kategori', 'Aktif')->get();

        return view('livewire.logistik.material.material-index', compact('materials', 'kategoris'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->status_material = 'Aktif'; // Default value
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditMode) {
            $material = Material::findOrFail($this->id_material);
            $material->update([
                'id_kategori_material' => $this->id_kategori_material,
                'nama_material'        => $this->nama_material,
                'satuan'               => $this->satuan,
                'spesifikasi'          => $this->spesifikasi,
                'standar_kualitas'     => $this->standar_kualitas,
                'status_material'      => $this->status_material,
            ]);
            session()->flash('message', 'Data Material berhasil diperbarui.');
        } else {
            // Create baru (ID MAT000x otomatis dibuat oleh Model)
            Material::create([
                'id_kategori_material' => $this->id_kategori_material,
                'nama_material'        => $this->nama_material,
                'satuan'               => $this->satuan,
                'spesifikasi'          => $this->spesifikasi,
                'standar_kualitas'     => $this->standar_kualitas,
                'status_material'      => $this->status_material,
            ]);
            session()->flash('message', 'Data Material baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);
        
        $this->id_material          = $material->id_material;
        $this->id_kategori_material = $material->id_kategori_material;
        $this->nama_material        = $material->nama_material;
        $this->satuan               = $material->satuan;
        $this->spesifikasi          = $material->spesifikasi;
        $this->standar_kualitas     = $material->standar_kualitas;
        $this->status_material      = $material->status_material;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        Material::findOrFail($id)->delete();
        session()->flash('message', 'Data Material berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->id_material          = '';
        $this->id_kategori_material = '';
        $this->nama_material        = '';
        $this->satuan               = '';
        $this->spesifikasi          = '';
        $this->standar_kualitas     = '';
        $this->status_material      = '';
    }
}