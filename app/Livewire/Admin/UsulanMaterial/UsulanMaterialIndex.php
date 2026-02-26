<?php

namespace App\Livewire\Admin\UsulanMaterial;

use App\Models\UsulanMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class UsulanMaterialIndex extends Component
{
    use WithPagination;

    // Properti untuk proses persetujuan (Approval)
    public $id_usulan_material;
    public $status_usulan;
    public $catatan_admin;
    
    // Properti UI
    public $search = '';
    public $isModalOpen = false;
    public $detailUsulan = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $usulan_materials = UsulanMaterial::with(['kategori', 'pengusul'])
            ->where(function($query) {
                $query->where('nama_material', 'like', '%' . $this->search . '%')
                      ->orWhere('id_usulan_material', 'like', '%' . $this->search . '%')
                      ->orWhereHas('pengusul', function($q) {
                          // SUDAH TEPAT: Menggunakan 'nama_lengkap' sesuai database Anda
                          $q->where('nama_lengkap', 'like', '%' . $this->search . '%'); 
                      });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.usulan-material.usulan-material-index', [
            'usulan_materials' => $usulan_materials
        ])->layout('layouts.app');
    }

    public function prosesUsulan($id)
    {
        $usulan = UsulanMaterial::with(['kategori', 'pengusul'])->findOrFail($id);
        
        $this->detailUsulan = $usulan;
        $this->id_usulan_material = $usulan->id_usulan_material;
        $this->status_usulan = $usulan->status_usulan;
        $this->catatan_admin = $usulan->catatan_admin;
        
        $this->isModalOpen = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->detailUsulan = null;
        $this->id_usulan_material = null;
        $this->status_usulan = '';
        $this->catatan_admin = '';
    }

    public function simpanProses()
    {
        $this->validate([
            'status_usulan' => 'required|in:Menunggu,Disetujui,Ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $usulan = UsulanMaterial::findOrFail($this->id_usulan_material);
        
        $usulan->update([
            'status_usulan' => $this->status_usulan,
            'catatan_admin' => $this->catatan_admin,
        ]);

        session()->flash('message', 'Usulan material berhasil diproses menjadi: ' . $this->status_usulan);
        $this->closeModal();
    }
}