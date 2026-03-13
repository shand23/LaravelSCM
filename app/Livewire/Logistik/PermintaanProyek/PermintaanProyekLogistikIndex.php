<?php

namespace App\Livewire\Logistik\PermintaanProyek;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PermintaanProyek;
use App\Models\StokBatchFifo;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PermintaanProyekLogistikIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = ''; // Untuk filter dropdown status

    // Variabel Modal Detail
    public $isModalOpen = false;
    public $permintaanTerpilih = null;
    public $detailBarang = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    // Buka Modal dan load detail barang yang diminta
    public function lihatDetail($id_permintaan)
    {
        $this->permintaanTerpilih = PermintaanProyek::with(['proyek', 'user', 'detailPermintaan.material'])->find($id_permintaan);
        
        if ($this->permintaanTerpilih) {
            $this->detailBarang = $this->permintaanTerpilih->detailPermintaan;
            $this->isModalOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->permintaanTerpilih = null;
        $this->detailBarang = [];
    }

    // FUNGSI INTI: Eksekusi pemotongan stok FIFO
    public function prosesPemenuhanStok()
    {
        if (!$this->permintaanTerpilih) return;

        $id_permintaan = $this->permintaanTerpilih->id_permintaan;

        DB::transaction(function () use ($id_permintaan) {
            $permintaan = PermintaanProyek::with('detailPermintaan')->lockForUpdate()->find($id_permintaan);
            
            $semuaTerpenuhi = true; 
            $adaYangDiproses = false;

            foreach ($permintaan->detailPermintaan as $detail) {
                // Hitung sisa yang belum terkirim
                $kebutuhanQty = $detail->jumlah_diminta - $detail->jumlah_terkirim;

                if ($kebutuhanQty > 0) {
                    // Cari stok FIFO
                    $batches = StokBatchFifo::where('id_material', $detail->id_material)
                                ->where('sisa_stok', '>', 0)
                                ->orderBy('tanggal_masuk', 'asc')
                                ->lockForUpdate() 
                                ->get();

                    $totalDiambilDariGudang = 0;

                    foreach ($batches as $batch) {
                        if ($kebutuhanQty == 0) break; 

                        if ($batch->sisa_stok >= $kebutuhanQty) {
                            $batch->sisa_stok -= $kebutuhanQty;
                            $totalDiambilDariGudang += $kebutuhanQty;
                            $kebutuhanQty = 0; 
                            $batch->save();
                        } else {
                            $kebutuhanQty -= $batch->sisa_stok;
                            $totalDiambilDariGudang += $batch->sisa_stok;
                            $batch->sisa_stok = 0;
                            $batch->save();
                        }
                    }

                    // Update jumlah terkirim di detail_permintaan_proyek
                    if ($totalDiambilDariGudang > 0) {
                        $detail->jumlah_terkirim += $totalDiambilDariGudang;
                        $detail->save();
                        $adaYangDiproses = true;
                    }

                    // Cek apakah masih ada kurang
                    if ($detail->jumlah_terkirim < $detail->jumlah_diminta) {
                        $semuaTerpenuhi = false;
                    }
                }
            }

            // Update Status Permintaan
            // Jika status awalnya 'Menunggu Persetujuan', ubah sesuai hasil proses
            if ($semuaTerpenuhi) {
                $permintaan->status_permintaan = 'Selesai';
            } elseif ($adaYangDiproses || !$semuaTerpenuhi) {
                $permintaan->status_permintaan = 'Diproses Sebagian';
                
                // Nanti logika insert ke tabel Pengajuan Pengadaan ditaruh di sini
            }
            
            $permintaan->save();
        });

        session()->flash('message', 'Pemrosesan stok FIFO berhasil dijalankan.');
        $this->closeModal();
    }

    public function render()
    {
        $query = PermintaanProyek::with(['proyek', 'user'])
            ->where(function($q) {
                $q->where('id_permintaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', function($q2) {
                      $q2->where('nama_proyek', 'like', '%' . $this->search . '%');
                  });
            });

        if ($this->filterStatus !== '') {
            $query->where('status_permintaan', $this->filterStatus);
        }

        return view('livewire.logistik.permintaan-proyek.permintaan-proyek-logistik-index', [
            'listPermintaan' => $query->latest('tanggal_permintaan')->paginate(10)
        ]);
    }
}