<?php

namespace App\Livewire\Manajemen\Approval; // Sudah diperbaiki menjadi Manajemen

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanMaterial;

class ApprovalIndex extends Component
{
    use WithPagination;

    public $selectedPengajuan = null;
    public $isModalDetailOpen = false;

    public function render()
    {
        $approvals = PengajuanMaterial::with(['proyek', 'userPengaju'])
            ->where('status_pengajuan', 'Menunggu')
            ->where('tipe_pengajuan', 'Permintaan Pelaksanaan')
            ->orderBy('tanggal_pengajuan', 'asc')
            ->paginate(10);

        return view('livewire.manajemen.approval.approval-index', compact('approvals'))
            ->layout('layouts.app');
    }

    public function showDetail($id)
    {
        $this->selectedPengajuan = PengajuanMaterial::with('detailPengajuan.material')->findOrFail($id);
        $this->isModalDetailOpen = true;
    }

    public function approve($id)
    {
        $pengajuan = PengajuanMaterial::findOrFail($id);
        $pengajuan->update(['status_pengajuan' => 'Disetujui PM']);
        
        session()->flash('message', 'Pengajuan ' . $id . ' telah disetujui.');
        $this->isModalDetailOpen = false;
    }

    public function closeModal()
    {
        $this->isModalDetailOpen = false;
    }
}