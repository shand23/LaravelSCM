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

    // ========== HELPER SPLIT ORDER ==========
    /**
     * Hitung sisa kebutuhan untuk setiap material dalam PR (berdasarkan semua RFQ yang sudah ada)
     */
    private function getRemainingQuantities($id_pengajuan)
    {
        $pr = PengajuanPembelian::with('detailPengajuan.material')->findOrFail($id_pengajuan);
        $pesananIds = Pesanan::where('id_pengajuan', $id_pengajuan)->pluck('id_pesanan');
        $totalDipesan = DetailPesanan::whereIn('id_pesanan', $pesananIds)
            ->select('id_material', DB::raw('SUM(jumlah_pesan) as total'))
            ->groupBy('id_material')
            ->pluck('total', 'id_material');

        $sisa = [];
        foreach ($pr->detailPengajuan as $detail) {
            $dipesan = $totalDipesan[$detail->id_material] ?? 0;
            $sisa[$detail->id_material] = max(0, $detail->jumlah_minta_beli - $dipesan);
        }
        return $sisa;
    }

    /**
     * Hitung sisa kebutuhan dengan mengecualikan satu RFQ tertentu (saat edit)
     */
    private function getRemainingQuantitiesExcluding($id_pengajuan, $excludePesananId)
    {
        $pr = PengajuanPembelian::with('detailPengajuan.material')->findOrFail($id_pengajuan);
        $pesananIds = Pesanan::where('id_pengajuan', $id_pengajuan)
            ->where('id_pesanan', '!=', $excludePesananId)
            ->pluck('id_pesanan');

        $totalDipesan = DetailPesanan::whereIn('id_pesanan', $pesananIds)
            ->select('id_material', DB::raw('SUM(jumlah_pesan) as total'))
            ->groupBy('id_material')
            ->pluck('total', 'id_material');

        $sisa = [];
        foreach ($pr->detailPengajuan as $detail) {
            $dipesan = $totalDipesan[$detail->id_material] ?? 0;
            $sisa[$detail->id_material] = max(0, $detail->jumlah_minta_beli - $dipesan);
        }
        return $sisa;
    }

    /**
     * Cek apakah semua material dalam PR sudah terpenuhi seluruhnya (tidak ada sisa)
     */
    private function checkIfPRIsFullyFulfilled($id_pengajuan)
    {
        $sisa = $this->getRemainingQuantities($id_pengajuan);
        foreach ($sisa as $qty) {
            if ($qty > 0) return false;
        }
        return true;
    }

    // ========== FUNGSI CETAK PDF ==========
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

    // ========== CREATE DARI PR (SPLIT ORDER AWARE) ==========
    public function createFromPR($id_pengajuan)
    {
        $this->reset(['id_supplier', 'items', 'edit_id']);
        $this->isEditMode = false;
        $this->resetErrorBag();

        $this->tanggal_pesanan = date('Y-m-d');
        $this->id_pengajuan = $id_pengajuan;

        // Ambil sisa kebutuhan setiap material
        $sisaKebutuhan = $this->getRemainingQuantities($id_pengajuan);

        $pr = PengajuanPembelian::with('detailPengajuan.material')->find($id_pengajuan);
        $this->items = [];

        if ($pr) {
            foreach ($pr->detailPengajuan as $detail) {
                $idMaterial = $detail->id_material;
                $sisa = $sisaKebutuhan[$idMaterial] ?? 0;
                $selected = $sisa > 0;

                $this->items[] = [
                    'id_material'      => $idMaterial,
                    'nama_material'    => $detail->material->nama_material,
                    'satuan'           => $detail->material->satuan,
                    'jumlah_asal'      => $detail->jumlah_minta_beli,
                    'sisa_kebutuhan'   => $sisa,
                    'jumlah_pesan'     => $selected ? $sisa : 0,
                    'selected'         => $selected,
                ];
            }
        }

        $this->isModalOpen = true;
    }

    // ========== EDIT RFQ (SPLIT ORDER AWARE) ==========
    public function edit($id)
    {
        $pesanan = Pesanan::with('detailPesanan')->findOrFail($id);

        if ($pesanan->id_user_pengadaan != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda hanya dapat mengubah RFQ yang Anda buat sendiri.');
            return;
        }

        if ($pesanan->status_pesanan !== 'Draft') {
            session()->flash('error', 'Hanya RFQ dengan status Draft yang dapat diedit.');
            return;
        }

        $this->isEditMode = true;
        $this->edit_id = $id;
        $this->id_pengajuan = $pesanan->id_pengajuan;
        $this->id_supplier = $pesanan->id_supplier;
        $this->tanggal_pesanan = $pesanan->tanggal_pesanan;

        // Hitung sisa kebutuhan dengan mengabaikan RFQ ini
        $sisaKebutuhan = $this->getRemainingQuantitiesExcluding($this->id_pengajuan, $id);

        $pr = PengajuanPembelian::with('detailPengajuan.material')->find($this->id_pengajuan);
        $detailPesananMap = $pesanan->detailPesanan->keyBy('id_material');
        $this->items = [];

        foreach ($pr->detailPengajuan as $detail) {
            $idMaterial = $detail->id_material;
            $maxAllowed = $sisaKebutuhan[$idMaterial] ?? 0;
            $currentQty = 0;
            if ($detailPesananMap->has($idMaterial)) {
                $currentQty = $detailPesananMap[$idMaterial]->jumlah_pesan;
                $maxAllowed += $currentQty;
            }

            $isSelected = $detailPesananMap->has($idMaterial) && $currentQty > 0;

            $this->items[] = [
                'id_material'      => $idMaterial,
                'nama_material'    => $detail->material->nama_material,
                'satuan'           => $detail->material->satuan,
                'jumlah_asal'      => $detail->jumlah_minta_beli,
                'sisa_kebutuhan'   => $maxAllowed, // untuk validasi frontend
                'jumlah_pesan'     => $isSelected ? $currentQty : 0,
                'selected'         => $isSelected,
            ];
        }

        $this->isModalOpen = true;
    }

    // ========== DELETE RFQ ==========
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

    // ========== STORE / UPDATE (SPLIT ORDER AWARE) ==========
    public function store()
    {
        $this->validate([
            'id_pengajuan' => 'required',
            'id_supplier'  => 'required',
            'tanggal_pesanan' => $this->isEditMode ? 'required|date' : 'required|date|after_or_equal:today',
            'items.*.selected' => 'boolean',
            'items.*.jumlah_pesan' => 'required|numeric|min:1',
        ]);

        $selectedItems = collect($this->items)->filter(fn($item) => $item['selected'] == true);

        if ($selectedItems->isEmpty()) {
            $this->addError('items', 'Pilih minimal satu material untuk dipesan ke supplier ini.');
            return;
        }

        // Validasi tambahan: jumlah_pesan tidak boleh melebihi sisa kebutuhan
        $sisaKebutuhan = $this->getRemainingQuantities($this->id_pengajuan);
        foreach ($selectedItems as $item) {
            $maxAllowed = $sisaKebutuhan[$item['id_material']] ?? 0;
            if ($item['jumlah_pesan'] > $maxAllowed) {
                $this->addError("items", "Jumlah pesan untuk {$item['nama_material']} melebihi sisa kebutuhan (maks {$maxAllowed} {$item['satuan']}).");
                return;
            }
        }

        DB::transaction(function () use ($selectedItems) {
            if ($this->isEditMode) {
                $pesanan = Pesanan::findOrFail($this->edit_id);
                $pesanan->update([
                    'id_supplier'    => $this->id_supplier,
                    'tanggal_pesanan' => $this->tanggal_pesanan,
                ]);
                DetailPesanan::where('id_pesanan', $pesanan->id_pesanan)->delete();
                foreach ($selectedItems as $item) {
                    DetailPesanan::create([
                        'id_pesanan'    => $pesanan->id_pesanan,
                        'id_material'   => $item['id_material'],
                        'jumlah_pesan'  => $item['jumlah_pesan'],
                    ]);
                }
                session()->flash('message', 'RFQ berhasil diperbarui!');
            } else {
                $pesanan = Pesanan::create([
                    'id_pengajuan'      => $this->id_pengajuan,
                    'id_supplier'       => $this->id_supplier,
                    'id_user_pengadaan' => Auth::user()->id_user,
                    'nomor_pesanan'     => $this->generateNomorPesanan(),
                    'tanggal_pesanan'   => $this->tanggal_pesanan,
                    'status_pesanan'    => 'Draft',
                ]);

                foreach ($selectedItems as $item) {
                    DetailPesanan::create([
                        'id_pesanan'    => $pesanan->id_pesanan,
                        'id_material'   => $item['id_material'],
                        'jumlah_pesan'  => $item['jumlah_pesan'],
                    ]);
                }

                // Cek apakah semua material sudah lunas
                if ($this->checkIfPRIsFullyFulfilled($this->id_pengajuan)) {
                    PengajuanPembelian::where('id_pengajuan', $this->id_pengajuan)
                        ->update(['status_pengajuan' => 'Proses RFQ']);
                    session()->flash('message', 'RFQ berhasil dibuat. Semua kebutuhan PR terpenuhi, status PR = Proses RFQ.');
                } else {
                    session()->flash('message', 'RFQ berhasil dibuat. Masih ada sisa kebutuhan, PR tetap di antrean.');
                }
            }
        });

        $this->closeModal();
    }

    // ========== RENDER ==========
    public function render()
    {
        $listPesanan = Pesanan::with(['supplier', 'pengajuan'])
            ->where('nomor_pesanan', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $listPRPending = PengajuanPembelian::with('permintaanProyek.proyek')
            ->where('status_pengajuan', 'Menunggu Pengadaan')
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $listSupplier = Supplier::orderBy('nama_supplier', 'asc')->get();

        return view('livewire.pengadaan.pesanan.pesanan-index', [
            'listPesanan'   => $listPesanan,
            'listPRPending' => $listPRPending,
            'listSupplier'  => $listSupplier,
        ]);
    }
}