<?php

namespace App\Livewire\Pengadaan\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class SupplierIndex extends Component
{
    use WithPagination;

    public $id_supplier, $nama_supplier, $kontak_person, $no_telepon, $email, $alamat, $status_supplier;
    
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
            'nama_supplier'   => 'required|string|max:150',
            'kontak_person'   => 'nullable|string|max:100',
            'no_telepon'      => 'required|string|max:20',
            'email'           => 'nullable|email|max:100',
            'alamat'          => 'nullable|string',
            'status_supplier' => 'required|in:Aktif,Nonaktif',
        ];
    }

    public function render()
    {
        $suppliers = Supplier::where('nama_supplier', 'like', '%' . $this->search . '%')
            ->orWhere('kontak_person', 'like', '%' . $this->search . '%')
            ->orderBy('id_supplier', 'desc')
            ->paginate(10);

        return view('livewire.pengadaan.supplier.supplier-index', compact('suppliers'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->status_supplier = 'Aktif'; // Default value
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditMode) {
            $supplier = Supplier::findOrFail($this->id_supplier);
            $supplier->update([
                'nama_supplier'   => $this->nama_supplier,
                'kontak_person'   => $this->kontak_person,
                'no_telepon'      => $this->no_telepon,
                'email'           => $this->email,
                'alamat'          => $this->alamat,
                'status_supplier' => $this->status_supplier,
            ]);
            session()->flash('message', 'Data Supplier berhasil diperbarui.');
        } else {
            Supplier::create([
                'nama_supplier'   => $this->nama_supplier,
                'kontak_person'   => $this->kontak_person,
                'no_telepon'      => $this->no_telepon,
                'email'           => $this->email,
                'alamat'          => $this->alamat,
                'status_supplier' => $this->status_supplier,
            ]);
            session()->flash('message', 'Data Supplier baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $this->id_supplier     = $supplier->id_supplier;
        $this->nama_supplier   = $supplier->nama_supplier;
        $this->kontak_person   = $supplier->kontak_person;
        $this->no_telepon      = $supplier->no_telepon;
        $this->email           = $supplier->email;
        $this->alamat          = $supplier->alamat;
        $this->status_supplier = $supplier->status_supplier;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        Supplier::findOrFail($id)->delete();
        session()->flash('message', 'Data Supplier berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->id_supplier     = '';
        $this->nama_supplier   = '';
        $this->kontak_person   = '';
        $this->no_telepon      = '';
        $this->email           = '';
        $this->alamat          = '';
        $this->status_supplier = '';
    }
}