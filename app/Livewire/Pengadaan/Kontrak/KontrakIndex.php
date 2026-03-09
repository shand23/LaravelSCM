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
        $this->total_nilai_kontrak = ($this->total_harga_negosiasi - $this->nominal_diskon) + max(0, $this->total_ongkir) + max(0, $this->total_ppn);
    }

    public function updatedItems() { $this->hitungTotal(); }
    public function updatedDiskonPersen() { $this->hitungTotal(); }
    public function updatedTotalOngkir() { $this->hitungTotal(); }
    public function updatedTotalPpn() { $this->hitungTotal(); }

    public function create() {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id) {
        $this->resetForm();
        $this->selected_id = $id;
        $kontrak = Kontrak::with('detailKontrak.material')->find($id);
        
        $this->id_pesanan = $kontrak->id_pesanan;
        $this->id_supplier = $kontrak->id_supplier;
        $this->tanggal_kontrak = $kontrak->tanggal_kontrak;
        $this->total_ongkir = $kontrak->total_ongkir;
        $this->total_ppn = $kontrak->total_ppn;
        
        if($kontrak->total_harga_negosiasi > 0) {
            $this->diskon_persen = ($kontrak->total_diskon / $kontrak->total_harga_negosiasi) * 100;
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
                    'id_user_pengadaan' => Auth::id(),
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
            DB::transaction(function () use ($id) {
                $kontrak = Kontrak::find($id);
                Pesanan::where('id_pesanan', $kontrak->id_pesanan)->update(['status_pesanan' => 'Proses Negosiasi']);
                DetailKontrak::where('id_kontrak', $id)->delete();
                $kontrak->delete();
            });
            session()->flash('message', 'PO dihapus & RFQ dikembalikan ke antrean negosiasi.');
        } catch (\Exception $e) { session()->flash('error', $e->getMessage()); }
    }

    public function markAsDisepakati($id) {
        try {
            $kontrak = Kontrak::findOrFail($id);
            $kontrak->update([
                'status_kontrak' => 'Disepakati'
            ]);
            session()->flash('message', 'PO berhasil disepakati dan dikunci!');
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
}