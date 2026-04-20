<?php

namespace App\Livewire\Pengadaan\Pesanan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\PengajuanPembelian; 
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 

#[Layout('layouts.app')]
class PesananIndex extends Component
{
    use WithPagination;

    // Properti UI
    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $edit_id = null;

    // Properti Form
    public $id_pengajuan;
    public $id_supplier;
    public $tanggal_pesanan;
    public $items = []; 

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->tanggal_pesanan = date('Y-m-d');
    }

    // --- FUNGSI CETAK PDF & UPDATE STATUS ---
    public function cetakPDF($id)
    {
        $pesanan = Pesanan::with(['supplier', 'pengajuan', 'detailPesanan.material'])->findOrFail($id);

        if ($pesanan->status_pesanan === 'Draft' && $pesanan->id_user_pengadaan != Auth::user()->id_user) {
        session()->flash('error', 'Akses Ditolak: Hanya pembuat RFQ yang dapat mencetak dokumen saat masih berstatus Draft.');
        return;
    }
        
        if ($pesanan->status_pesanan === 'Draft') {
            $pesanan->update(['status_pesanan' => 'Proses Negosiasi']);
        }

        $namaFile = 'RFQ-' . str_replace('/', '-', $pesanan->nomor_pesanan) . '.pdf';
        $pdf = Pdf::loadView('livewire.pengadaan.pesanan.pdf-rfq', ['pesanan' => $pesanan]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $namaFile);
    }

    // --- MENGGANTIKAN FUNGSI CREATE LAMA ---
    public function createFromPR($id_pengajuan)
    {
        $this->reset(['id_supplier', 'items', 'edit_id']);
        $this->isEditMode = false;
        $this->resetErrorBag();
        
        $this->tanggal_pesanan = date('Y-m-d');
        $this->id_pengajuan = $id_pengajuan;

        // Otomatis load material dari PR yang dipilih
        $this->items = [];
        $pr = PengajuanPembelian::with('detailPengajuan.material')->find($id_pengajuan);
        
        if ($pr) {
            foreach ($pr->detailPengajuan as $detail) {
                $this->items[] = [
                    'id_material' => $detail->id_material,
                    'nama_material' => $detail->material->nama_material,
                    'satuan' => $detail->material->satuan,
                    'jumlah_asal' => $detail->jumlah_minta_beli,
                    'jumlah_pesan' => $detail->jumlah_minta_beli, 
                    'selected' => true 
                ];
            }
        }

        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $pesanan = Pesanan::with('detailPesanan')->findOrFail($id);

        if ($pesanan->id_user_pengadaan != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda hanya dapat mengubah RFQ yang Anda buat sendiri.');
            return;
        }

        if ($pesanan->status_pesanan === 'Berlanjut ke Kontrak') {
            session()->flash('error', 'Pesanan yang sudah berlanjut ke kontrak tidak dapat diedit.');
            return;
        }

        $this->isEditMode = true;
        $this->edit_id = $id;
        $this->id_pengajuan = $pesanan->id_pengajuan;
        $this->id_supplier = $pesanan->id_supplier;
        $this->tanggal_pesanan = $pesanan->tanggal_pesanan;

        $pr = PengajuanPembelian::with('detailPengajuan.material')->find($this->id_pengajuan);
        $this->items = [];
        
        $detailPesananMap = $pesanan->detailPesanan->keyBy('id_material');

        if ($pr) {
            foreach ($pr->detailPengajuan as $detail) {
                $isSelected = $detailPesananMap->has($detail->id_material);
                $this->items[] = [
                    'id_material' => $detail->id_material,
                    'nama_material' => $detail->material->nama_material,
                    'satuan' => $detail->material->satuan,
                    'jumlah_asal' => $detail->jumlah_minta_beli,
                    'jumlah_pesan' => $isSelected ? $detailPesananMap[$detail->id_material]->jumlah_pesan : $detail->jumlah_minta_beli,
                    'selected' => $isSelected
                ];
            }
        }

        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        if ($pesanan->id_user_pengadaan != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk menghapus RFQ ini.');
            return;
        }

        if ($pesanan->status_pesanan === 'Berlanjut ke Kontrak') {
            session()->flash('error', 'Pesanan yang sudah berlanjut ke kontrak tidak dapat dihapus.');
            return;
        }

        $id_pengajuan = $pesanan->id_pengajuan;

        DB::transaction(function () use ($pesanan, $id_pengajuan) {
            DetailPesanan::where('id_pesanan', $pesanan->id_pesanan)->delete();
            $pesanan->delete();

            $hasOtherPesanan = Pesanan::where('id_pengajuan', $id_pengajuan)->exists();
            
            if (!$hasOtherPesanan) {
                PengajuanPembelian::where('id_pengajuan', $id_pengajuan)
                    ->update(['status_pengajuan' => 'Menunggu Pengadaan']);
            }
        });

        session()->flash('message', 'Pesanan (RFQ) berhasil dihapus dan dibatalkan.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetErrorBag();
    }

    private function generateNomorPesanan()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $count = Pesanan::whereYear('created_at', $tahun)
                        ->whereMonth('created_at', $bulan)
                        ->count() + 1;
        
        return "RFQ/{$tahun}/{$bulan}/" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function store()
    {
        $this->validate([
            'id_pengajuan' => 'required',
            'id_supplier' => 'required',
            'tanggal_pesanan' => $this->isEditMode ? 'required|date' : 'required|date|after_or_equal:today', 
            'items.*.selected' => 'boolean',
            'items.*.jumlah_pesan' => 'required|numeric|min:1',
        ]);

        $selectedItems = collect($this->items)->filter(function ($item) {
            return $item['selected'] == true;
        });

        if ($selectedItems->isEmpty()) {
            $this->addError('items', 'Pilih minimal satu material untuk dipesan ke supplier ini.');
            return;
        }

        DB::transaction(function () use ($selectedItems) {
            if ($this->isEditMode) {
                $pesanan = Pesanan::findOrFail($this->edit_id);
                $pesanan->update([
                    'id_supplier' => $this->id_supplier,
                    'tanggal_pesanan' => $this->tanggal_pesanan,
                ]);

                DetailPesanan::where('id_pesanan', $pesanan->id_pesanan)->delete();
                
                foreach ($selectedItems as $item) {
                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_material' => $item['id_material'],
                        'jumlah_pesan' => $item['jumlah_pesan'],
                    ]);
                }
                
                session()->flash('message', 'Pesanan (RFQ) berhasil diperbarui!');
            } else {
                $pesanan = Pesanan::create([
                    'id_pengajuan' => $this->id_pengajuan,
                    'id_supplier' => $this->id_supplier,
                    'id_user_pengadaan' => Auth::user()->id_user, // Sesuaikan auth Anda
                    'nomor_pesanan' => $this->generateNomorPesanan(),
                    'tanggal_pesanan' => $this->tanggal_pesanan,
                    'status_pesanan' => 'Draft',
                ]);

                foreach ($selectedItems as $item) {
                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_material' => $item['id_material'],
                        'jumlah_pesan' => $item['jumlah_pesan'],
                    ]);
                }

                // Update status PR agar hilang dari antrean "Menunggu Pengadaan"
                PengajuanPembelian::where('id_pengajuan', $this->id_pengajuan)
                    ->update(['status_pengajuan' => 'Proses RFQ']);
                    
                session()->flash('message', 'Pesanan (RFQ) berhasil dibuat dan berstatus Draft!');
            }
        });

        $this->closeModal();
    }

    public function render()
    {
        // Data tabel riwayat Pesanan (Bawah)
        $listPesanan = Pesanan::with(['supplier', 'pengajuan'])
            ->where('nomor_pesanan', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Data tabel Antrean PR (Atas) - Hanya yang belum dibuat RFQ
        $listPRPending = PengajuanPembelian::with('permintaanProyek.proyek')
            ->where('status_pengajuan', 'Menunggu Pengadaan')
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $listSupplier = Supplier::orderBy('nama_supplier', 'asc')->get();

        return view('livewire.pengadaan.pesanan.pesanan-index', [
            'listPesanan' => $listPesanan,
            'listPRPending' => $listPRPending,
            'listSupplier' => $listSupplier,
        ]);
    }
}