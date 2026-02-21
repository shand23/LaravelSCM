<?php

namespace App\Livewire\Admin\Material;

use App\Models\Material;
use App\Models\KategoriMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialIndex extends Component
{
    use WithPagination;

    // --- VARIABEL FORM ---
    public $id_material;          // Primary Key (Diset null saat create agar Model generate otomatis)
    public $id_kategori_material; // Foreign Key (Dropdown)
    public $nama_material;
    public $satuan;
    public $spesifikasi;
    public $standar_kualitas;
    public $status_material = 'Aktif'; // Default value

    // --- STATE MANAGEMENT ---
    public $isModalOpen = false;  // Kontrol buka/tutup modal
    public $isEditMode = false;   // Penanda apakah sedang Edit atau Tambah Baru

    // --- SEARCH (OPSIONAL) ---
    // Jika ingin fitur pencarian, tambahkan wire:model="search" di input search di blade
    public $search = ''; 

    // Reset pagination saat melakukan pencarian
    protected $updatesQueryString = ['search'];
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // ... (kode query material tetap sama) ...
        $materials = Material::with('kategori')
            // ...
            ->paginate(10);
            
        $kategori_list = KategoriMaterial::where('status_kategori', 'Aktif')->get();

        return view('livewire.admin.material.material-index', [
            'materials' => $materials,
            'kategori_list' => $kategori_list
        ])->layout('layouts.app'); // <--- TAMBAHKAN INI
    }
    /**
     * Membuka Modal untuk Tambah Data Baru
     */
    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->openModal();
    }

    /**
     * Membuka Modal untuk Edit Data
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        
        // Isi form dengan data dari database
        $this->id_material = $material->id_material;
        $this->id_kategori_material = $material->id_kategori_material;
        $this->nama_material = $material->nama_material;
        $this->satuan = $material->satuan;
        $this->spesifikasi = $material->spesifikasi;
        $this->standar_kualitas = $material->standar_kualitas;
        $this->status_material = $material->status_material;

        $this->isEditMode = true;
        $this->openModal();
    }

    /**
     * Menyimpan Data (Bisa Create atau Update)
     */
    public function store()
    {
        // 1. Validasi Input
        $this->validate([
            'id_kategori_material' => 'required',
            'nama_material' => 'required|string|max:150',
            'satuan' => 'required|string|max:50',
            'status_material' => 'required|in:Aktif,Nonaktif',
            'standar_kualitas' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
        ]);

        // 2. Simpan ke Database
        // logic: Jika id_material ada isinya -> Update. Jika null -> Create (Model akan Auto Generate ID)
        Material::updateOrCreate(
            ['id_material' => $this->id_material], 
            [
                'id_kategori_material' => $this->id_kategori_material,
                'nama_material' => $this->nama_material,
                'satuan' => $this->satuan,
                'spesifikasi' => $this->spesifikasi,
                'standar_kualitas' => $this->standar_kualitas,
                'status_material' => $this->status_material,
            ]
        );

        // 3. Beri Notifikasi Flash Message
        session()->flash('message', $this->isEditMode ? 'Material berhasil diperbarui.' : 'Material berhasil ditambahkan.');

        // 4. Tutup Modal & Bersihkan Form
        $this->closeModal();
        $this->resetInputFields();
    }

    /**
     * Menghapus Data
     */
    public function delete($id)
    {
        $material = Material::find($id);
        if ($material) {
            $material->delete();
            session()->flash('message', 'Material berhasil dihapus.');
        }
    }

    // --- HELPER METHODS ---

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields(); // Bersihkan error message jika ada
    }

    private function resetInputFields()
    {
        $this->id_material = null; // Penting: Null-kan ID agar terdeteksi sebagai 'Create Baru'
        $this->id_kategori_material = '';
        $this->nama_material = '';
        $this->satuan = '';
        $this->spesifikasi = '';
        $this->standar_kualitas = '';
        $this->status_material = 'Aktif';
        $this->resetErrorBag(); // Hilangkan pesan error validasi sebelumnya
    }
}