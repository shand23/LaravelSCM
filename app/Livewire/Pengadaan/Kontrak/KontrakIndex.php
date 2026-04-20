<?php

namespace App\Livewire\Pengadaan\Kontrak;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Kontrak;
use App\Models\DetailKontrak;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class KontrakIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isDetailModalOpen = false;
    public $isEditMode = false;
    public $selected_id;
    public $kontrakDetailData;

    public $id_pesanan, $id_supplier, $nama_supplier_text, $tanggal_kontrak;
    public $total_harga_negosiasi = 0;
    public $diskon_persen = 0;
    public $nominal_diskon = 0;
    public $total_ongkir = 0;
    public $total_ppn = 0;
    public $ppn_persen = 11; // Untuk menampung input misal: 11 atau 12
    public $dpp_nilai = 0;  // Dasar Pengenaan Pajak
    public $total_nilai_kontrak = 0;
    public $items = [];

    public function mount() {
        $this->tanggal_kontrak = date('Y-m-d');
    }

    public function updatedIdPesanan($id) {
        $this->items = [];
        if ($id) {
            $pesanan = Pesanan::with(['supplier', 'detailPesanan.material'])->find($id);
            if ($pesanan) {
                $this->id_supplier = $pesanan->id_supplier;
                $this->nama_supplier_text = $pesanan->supplier->nama_supplier ?? '-';
                foreach ($pesanan->detailPesanan as $detail) {
                    $this->items[] = [
                        'id_material' => $detail->id_material,
                        'nama_material' => $detail->material->nama_material,
                        'satuan' => $detail->material->satuan,
                        'jumlah_final' => $detail->jumlah_pesan,
                        'harga_negosiasi_satuan' => 0,
                        'subtotal' => 0
                    ];
                }
            }
        }
        $this->hitungTotal();
    }

   public function hitungTotal() {
        $this->total_harga_negosiasi = 0;
        foreach ($this->items as $index => $item) {
            $qty = max(0, floatval($item['jumlah_final'] ?? 0));
            $harga = max(0, floatval($item['harga_negosiasi_satuan'] ?? 0));
            $subtotal = $qty * $harga;
            $this->items[$index]['subtotal'] = $subtotal;
            $this->total_harga_negosiasi += $subtotal;
        }
        
        $persen = min(100, max(0, floatval($this->diskon_persen ?: 0)));
        $this->nominal_diskon = ($persen / 100) * $this->total_harga_negosiasi;
        
        // --- LOGIKA BARU PPN & DPP ---
        // 1. Dapatkan Nilai DPP (Subtotal - Diskon)
        $this->dpp_nilai = $this->total_harga_negosiasi - $this->nominal_diskon;
        
        // 2. Hitung Nominal PPN dari Persentase
        $ppn_rate = min(100, max(0, floatval($this->ppn_persen ?: 0)));
        $this->total_ppn = ($ppn_rate / 100) * $this->dpp_nilai;

        // 3. Grand Total Akhir
        $this->total_nilai_kontrak = $this->dpp_nilai + max(0, $this->total_ongkir) + $this->total_ppn;
        // -----------------------------
    }
    public function updatedItems() { $this->hitungTotal(); }
    public function updatedDiskonPersen() { $this->hitungTotal(); }
    public function updatedTotalOngkir() { $this->hitungTotal(); }
   public function updatedPpnPersen() { $this->hitungTotal(); }

    public function create() {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id) {
        $this->resetForm();
        $this->selected_id = $id;
        $kontrak = Kontrak::with('detailKontrak.material')->find($id);
        
        if ($kontrak->id_user_pengadaan != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda hanya dapat mengubah kontrak yang Anda buat sendiri.');
            return;
        }

        $this->id_pesanan = $kontrak->id_pesanan;
        $this->id_supplier = $kontrak->id_supplier;
        $this->tanggal_kontrak = $kontrak->tanggal_kontrak;
        $this->total_ongkir = $kontrak->total_ongkir;
        $this->total_ppn = $kontrak->total_ppn;
        
      $dpp = $kontrak->total_harga_negosiasi - $kontrak->nominal_diskon;
        if($dpp > 0) {
            // Menghitung balik persen dari nominal yang tersimpan di DB
            $this->ppn_persen = round(($kontrak->total_ppn / $dpp) * 100); 
        } else {
            $this->ppn_persen = 0;
        }

        foreach ($kontrak->detailKontrak as $detail) {
            $this->items[] = [
                'id_material' => $detail->id_material,
                'nama_material' => $detail->material->nama_material,
                'satuan' => $detail->material->satuan,
                'jumlah_final' => $detail->jumlah_final,
                'harga_negosiasi_satuan' => $detail->harga_negosiasi_satuan,
                'subtotal' => $detail->jumlah_final * $detail->harga_negosiasi_satuan
            ];
        }

        $this->isEditMode = true;
        $this->isModalOpen = true;
        $this->hitungTotal();
    }

    public function showDetail($id) {
        $this->kontrakDetailData = Kontrak::with(['supplier', 'detailKontrak.material', 'pesanan'])->find($id);
        $this->isDetailModalOpen = true;
    }

    public function store() {
        $this->validateData();
        try {
            DB::transaction(function () {
                $kontrak = Kontrak::create([
                    'id_pesanan' => $this->id_pesanan,
                    'id_supplier' => $this->id_supplier,
                    'id_user_pengadaan' => Auth::user()->id_user,
                    'nomor_kontrak' => "PO/" . date('Ymd') . "/" . strtoupper(Str::random(4)),
                    'tanggal_kontrak' => $this->tanggal_kontrak,
                    'total_harga_awal' => $this->total_harga_negosiasi,
                    'total_harga_negosiasi' => $this->total_harga_negosiasi,
                    'total_diskon' => $this->nominal_diskon,
                    'total_ongkir' => $this->total_ongkir ?: 0,
                    'total_ppn' => $this->total_ppn ?: 0,
                    'total_nilai_kontrak' => $this->total_nilai_kontrak,
                    'status_kontrak' => 'Draft',
                ]);

                foreach ($this->items as $item) {
                    DetailKontrak::create([
                        'id_kontrak' => $kontrak->id_kontrak,
                        'id_material' => $item['id_material'],
                        'jumlah_final' => $item['jumlah_final'],
                        'harga_negosiasi_satuan' => $item['harga_negosiasi_satuan'],
                    ]);
                }
                Pesanan::where('id_pesanan', $this->id_pesanan)->update(['status_pesanan' => 'Berlanjut ke Kontrak']);
            });
            session()->flash('message', 'PO Berhasil Diterbitkan!');
            $this->closeModal();
        } catch (\Exception $e) { session()->flash('error', $e->getMessage()); }
    }

    public function update() {
        $this->validateData();
        try {
            DB::transaction(function () {
                $kontrak = Kontrak::find($this->selected_id);
                $kontrak->update([
                    'tanggal_kontrak' => $this->tanggal_kontrak,
                    'total_harga_negosiasi' => $this->total_harga_negosiasi,
                    'total_diskon' => $this->nominal_diskon,
                    'total_ongkir' => $this->total_ongkir ?: 0,
                    'total_ppn' => $this->total_ppn ?: 0,
                    'total_nilai_kontrak' => $this->total_nilai_kontrak,
                ]);

                DetailKontrak::where('id_kontrak', $this->selected_id)->delete();
                foreach ($this->items as $item) {
                    DetailKontrak::create([
                        'id_kontrak' => $this->selected_id,
                        'id_material' => $item['id_material'],
                        'jumlah_final' => $item['jumlah_final'],
                        'harga_negosiasi_satuan' => $item['harga_negosiasi_satuan'],
                    ]);
                }
            });
            session()->flash('message', 'PO Berhasil Diperbarui!');
            $this->closeModal();
        } catch (\Exception $e) { session()->flash('error', $e->getMessage()); }
    }

   public function delete($id) {
        try {
            // 1. Cari data kontrak terlebih dahulu
            $kontrak = Kontrak::findOrFail($id);

            // 2. VALIDASI HAK AKSES
            // Hanya pembuat (id_user_pengadaan) yang boleh menghapus
            if ($kontrak->id_user_pengadaan != Auth::user()->id_user) {
                session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk menghapus kontrak ini.');
                return;
            }

            // 3. PROSES PENGHAPUSAN DALAM TRANSAKSI
            DB::transaction(function () use ($kontrak, $id) {
                // Ambil data pesanan terkait untuk mendapatkan id_pengajuan
                $pesanan = Pesanan::find($kontrak->id_pesanan);
                
                if ($pesanan) {
                    // Update status Pesanan (RFQ) kembali ke Proses Negosiasi
                    $pesanan->update(['status_pesanan' => 'Proses Negosiasi']);

                    // Update status Pengajuan (PR) kembali ke "Proses RFQ"
                    \App\Models\PengajuanPembelian::where('id_pengajuan', $pesanan->id_pengajuan)
                        ->update(['status_pengajuan' => 'Proses RFQ']);
                }

                // Hapus Detail dan Data Kontrak
                DetailKontrak::where('id_kontrak', $id)->delete();
                $kontrak->delete();
            });

            session()->flash('message', 'PO dihapus. Status RFQ dan PR telah dikembalikan ke antrean.');
        } catch (\Exception $e) { 
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage()); 
        }
    }
    public function markAsDisepakati($id) {
    try {
        // Ambil data kontrak beserta relasi pesanannya untuk mendapatkan ID Pengajuan
        $kontrak = Kontrak::with('pesanan')->findOrFail($id);

        // --- 1. VALIDASI HAK AKSES ---
        // Pastikan hanya pembuat kontrak (id_user_pengadaan) yang bisa menyepakati
        if ($kontrak->id_user_pengadaan != auth()->user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda hanya bisa menyepakati kontrak yang Anda buat sendiri.');
            return;
        }

        // --- 2. PROSES UPDATE DENGAN TRANSAKSI ---
        DB::transaction(function () use ($kontrak) {
            // Update status kontrak menjadi Disepakati
            $kontrak->update([
                'status_kontrak' => 'Disepakati'
            ]);

            // Update status_pengajuan di tabel pengajuan_pembelian menjadi "PO Dibuat"
            // Asumsi: Kontrak memiliki relasi 'pesanan' dan pesanan memiliki kolom 'id_pengajuan'
            if ($kontrak->pesanan && $kontrak->pesanan->id_pengajuan) {
                \App\Models\PengajuanPembelian::where('id_pengajuan', $kontrak->pesanan->id_pengajuan)
                    ->update(['status_pengajuan' => 'PO Dibuat']);
            }
        });

        session()->flash('message', 'PO berhasil disepakati dan dikunci! Status PR kini: PO Dibuat.');
        
    } catch (\Exception $e) { 
        session()->flash('error', 'Gagal mengupdate status: ' . $e->getMessage()); 
    }
}

    private function validateData() {
        return $this->validate([
            'id_pesanan' => 'required',
            'tanggal_kontrak' => 'required|date|after_or_equal:today',
            'diskon_persen' => 'numeric|min:0|max:100',
            'items.*.harga_negosiasi_satuan' => 'required|numeric|min:0',
            'items.*.jumlah_final' => 'required|numeric|min:1',
        ]);
    }

    private function resetForm() {
        $this->reset(['id_pesanan', 'id_supplier', 'nama_supplier_text', 'items', 'diskon_persen', 'nominal_diskon', 'total_ongkir', 'total_ppn', 'selected_id']);
        $this->tanggal_kontrak = date('Y-m-d');
        $this->ppn_persen = 11;
    }

    public function closeModal() { $this->isModalOpen = false; }
    
    public function closeDetailModal() {
        $this->isDetailModalOpen = false;
        $this->kontrakDetailData = null;
    }

    public function render() {
        return view('livewire.pengadaan.kontrak.kontrak-index', [
            'listKontrak' => Kontrak::with(['supplier'])
                                ->where('nomor_kontrak', 'like', "%{$this->search}%")
                                ->orderBy('created_at', 'desc')
                                ->paginate(10),
            'listRFQ' => Pesanan::where('status_pesanan', 'Proses Negosiasi')
                                ->orWhere('id_pesanan', $this->id_pesanan)
                                ->get()
        ]);
    }

    public function printPO($id)
    {
        // Ambil data kontrak lengkap dengan relasi supplier dan detail materialnya
        $kontrak = Kontrak::with(['supplier', 'detailKontrak.material', 'pesanan'])->find($id);

        if (!$kontrak) {
            session()->flash('error', 'Data tidak ditemukan.');
            return;
        }

        // Load view yang akan kita buat nanti
        $pdf = Pdf::loadView('livewire.pengadaan.kontrak.pdf-po', [
            'kontrak' => $kontrak
        ])->setPaper('a4', 'portrait');

        // Download file dengan nama nomor kontrak
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'PO-' . str_replace('/', '-', $kontrak->nomor_kontrak) . '.pdf');
    }
    
}