<?php

namespace App\Livewire\Admin\Supplier;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination;

    // Properti Data (Sesuai kolom database)
    public $id_supplier, $nama_supplier, $alamat, $kota, $kontak_person, $no_telepon, $email;
    public $status_supplier = 'Aktif';

    // Properti UI
    public $isModalOpen = false;
    public $isEditMode = false;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Query Pencarian disesuaikan dengan kolom yang ada
        $suppliers = Supplier::where(function($query) {
                $query->where('nama_supplier', 'like', '%' . $this->search . '%')
                      ->orWhere('kota', 'like', '%' . $this->search . '%')
                      ->orWhere('kontak_person', 'like', '%' . $this->search . '%')
                      ->orWhere('id_supplier', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.supplier.supplier-index', [
            'suppliers' => $suppliers
        ])->layout('layouts.app');
    }

    // --- MANAJEMEN MODAL & INPUT ---

    public function create()
    {
        $this->resetInputFields();
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
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->id_supplier = null;
        $this->nama_supplier = '';
        $this->alamat = '';
        $this->kota = '';
        $this->kontak_person = '';
        $this->no_telepon = '';
        $this->email = '';
        $this->status_supplier = 'Aktif';
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    // --- CRUD LOGIC ---

    public function store()
    {
        // 1. Validasi
        $this->validate([
            'nama_supplier' => 'required|string|max:150',
            'alamat'        => 'nullable|string',
            'kota'          => 'nullable|string|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'no_telepon'    => 'required|numeric', // Wajib diisi agar bisa dihubungi
            'email'         => 'nullable|email',
            'status_supplier' => 'required'
        ]);

        // 2. Generate ID
        if ($this->isEditMode) {
            $currentId = $this->id_supplier;
            $pesan = 'Data supplier berhasil diperbarui.';
        } else {
            // ID: SUP-YYYYMMDD-XXX
            $dateCode = date('Ymd');
            // Cek jumlah data hari ini untuk urutan
            $countToday = Supplier::where('id_supplier', 'like', 'SUP-' . $dateCode . '%')->count() + 1;
            $currentId = 'SUP-' . $dateCode . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
            $pesan = 'Supplier baru berhasil ditambahkan.';
        }

        // 3. Simpan
        Supplier::updateOrCreate(
            ['id_supplier' => $currentId],
            [
                'nama_supplier' => $this->nama_supplier,
                'alamat'        => $this->alamat,
                'kota'          => $this->kota,
                'kontak_person' => $this->kontak_person,
                'no_telepon'    => $this->no_telepon,
                'email'         => $this->email,
                'status_supplier' => $this->status_supplier,
            ]
        );

        session()->flash('message', $pesan);
        $this->closeModal();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->id_supplier = $supplier->id_supplier;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->alamat = $supplier->alamat;
        $this->kota = $supplier->kota;
        $this->kontak_person = $supplier->kontak_person;
        $this->no_telepon = $supplier->no_telepon;
        $this->email = $supplier->email;
        $this->status_supplier = $supplier->status_supplier;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);

        if ($supplier) {
            if ($supplier->status_supplier == 'Aktif') {
                session()->flash('error', 'Gagal: Ubah status ke Nonaktif dulu sebelum menghapus!');
                return;
            }
            $supplier->delete();
            session()->flash('message', 'Data supplier berhasil dihapus.');
        }
    }
}