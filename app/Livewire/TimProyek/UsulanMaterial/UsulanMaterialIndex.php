<?php

namespace App\Livewire\TimProyek\UsulanMaterial;

use App\Models\UsulanMaterial;
use App\Models\KategoriMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UsulanMaterialIndex extends Component
{
    use WithPagination;

    // Properti Form
    public $id_usulan_material, $id_kategori_material, $nama_material, $satuan, $spesifikasi;
    
    // Properti UI
    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Reset halaman saat mencari
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // AMAN: Ambil ID user yang sedang login (Sesuaikan dengan nama primary key tabel users Anda, misal id_user)
        $currentUserId = Auth::user()->id_user ?? Auth::id();

        // Query data khusus milik user yang sedang login
        $usulan_materials = UsulanMaterial::with('kategori')
            ->where('id_user_pengusul', $currentUserId) // <--- FILTER KEAMANAN
            ->where(function($query) {
                $query->where('nama_material', 'like', '%' . $this->search . '%')
                      ->orWhere('id_usulan_material', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.tim-proyek.usulan-material.usulan-material-index', [
            'usulan_materials' => $usulan_materials,
            'daftar_kategori'  => KategoriMaterial::all()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->id_usulan_material = null;
        $this->id_kategori_material = '';
        $this->nama_material = '';
        $this->satuan = '';
        $this->spesifikasi = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        // 1. Validasi Input
        $this->validate([
            'id_kategori_material' => 'required',
            'nama_material'        => 'required|string|max:150',
            'satuan'               => 'required|string|max:50',
            'spesifikasi'          => 'nullable|string',
        ]);

        $currentUserId = Auth::user()->id_user ?? Auth::id();

        // 2. Simpan atau Update Data
        if ($this->isEditMode) {
            // Pastikan data milik user tersebut dan status masih 'Menunggu'
            $usulan = UsulanMaterial::where('id_usulan_material', $this->id_usulan_material)
                                    ->where('id_user_pengusul', $currentUserId)
                                    ->firstOrFail();

            if ($usulan->status_usulan !== 'Menunggu') {
                session()->flash('error', 'Usulan yang sudah diproses tidak dapat diubah.');
                $this->closeModal();
                return;
            }

            $usulan->update([
                'id_kategori_material' => $this->id_kategori_material,
                'nama_material'        => $this->nama_material,
                'satuan'               => $this->satuan,
                'spesifikasi'          => $this->spesifikasi,
            ]);
            session()->flash('message', 'Usulan material berhasil diperbarui.');
            
        } else {
            // ID usulan (REQxxx) otomatis dibuat di fungsi boot() Model
            UsulanMaterial::create([
                'id_user_pengusul'     => $currentUserId,
                'id_kategori_material' => $this->id_kategori_material,
                'nama_material'        => $this->nama_material,
                'satuan'               => $this->satuan,
                'spesifikasi'          => $this->spesifikasi,
                'status_usulan'        => 'Menunggu',
            ]);
            session()->flash('message', 'Usulan material baru berhasil dikirim.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $currentUserId = Auth::user()->id_user ?? Auth::id();
        
        // Ambil data, pastikan milik user tersebut
        $usulan = UsulanMaterial::where('id_usulan_material', $id)
                                ->where('id_user_pengusul', $currentUserId)
                                ->firstOrFail();

        // Cek status, kalau sudah bukan Menunggu, tolak edit
        if ($usulan->status_usulan !== 'Menunggu') {
            session()->flash('error', 'Gagal: Usulan yang sudah diproses admin tidak dapat diedit.');
            return;
        }

        // Isi ke properti form
        $this->id_usulan_material   = $usulan->id_usulan_material;
        $this->id_kategori_material = $usulan->id_kategori_material;
        $this->nama_material        = $usulan->nama_material;
        $this->satuan               = $usulan->satuan;
        $this->spesifikasi          = $usulan->spesifikasi;
        
        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $currentUserId = Auth::user()->id_user ?? Auth::id();
        
        $usulan = UsulanMaterial::where('id_usulan_material', $id)
                                ->where('id_user_pengusul', $currentUserId)
                                ->first();

        if ($usulan) {
            // Hanya bisa dihapus jika masih Menunggu
            if ($usulan->status_usulan !== 'Menunggu') {
                session()->flash('error', 'Gagal: Usulan yang sudah diproses admin tidak dapat dibatalkan/dihapus.');
                return;
            }
            
            $usulan->delete();
            session()->flash('message', 'Usulan berhasil dibatalkan dan dihapus.');
        }
    }
}