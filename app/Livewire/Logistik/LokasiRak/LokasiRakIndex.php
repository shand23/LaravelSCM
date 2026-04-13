<?php

namespace App\Livewire\Logistik\LokasiRak;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\MasterLokasiRak;

#[Layout('layouts.app')]
class LokasiRakIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;

    // Form Variables
    public $nama_lokasi, $AREA, $keterangan;
    public $edit_id = null;

    protected $rules = [
        'nama_lokasi' => 'required|min:3',
        'AREA' => 'required',
        'keterangan' => 'nullable',
    ];

    public function render()
    {
        $lokasi = MasterLokasiRak::where('nama_lokasi', 'like', '%' . $this->search . '%')
                    ->orWhere('AREA', 'like', '%' . $this->search . '%')
                    ->orWhere('id_lokasi', 'like', '%' . $this->search . '%')
                    ->latest()
                    ->paginate(10);

        return view('livewire.logistik.lokasi-rak.lokasi-rak-index', [
            'listLokasi' => $lokasi
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $data = MasterLokasiRak::findOrFail($id);
        $this->edit_id = $data->id_lokasi;
        $this->nama_lokasi = $data->nama_lokasi;
        $this->AREA = $data->AREA;
        $this->keterangan = $data->keterangan;
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        MasterLokasiRak::updateOrCreate(
            ['id_lokasi' => $this->edit_id],
            [
                'nama_lokasi' => $this->nama_lokasi,
                'AREA' => $this->AREA,
                'keterangan' => $this->keterangan,
            ]
        );

        session()->flash('message', $this->edit_id ? 'Lokasi Rak berhasil diupdate!' : 'Lokasi Rak berhasil ditambahkan!');
        $this->closeModal();
    }

    public function delete($id)
    {
        MasterLokasiRak::findOrFail($id)->delete();
        session()->flash('message', 'Lokasi Rak berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->edit_id = null;
        $this->nama_lokasi = '';
        $this->AREA = '';
        $this->keterangan = '';
        $this->resetErrorBag();
    }
}