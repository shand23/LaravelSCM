<?php

namespace App\Livewire\Logistik\Penerimaan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\PenerimaanMaterial;
use App\Models\DetailPenerimaan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PenerimaanIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isModalOpen = false;
    
    // Form Input Header
    public $id_pengiriman;
    public $tanggal_terima;
    public $nomor_surat_jalan;
    public $status_penerimaan = 'Diterima Penuh';
    public $foto_bukti_rusak; // Properti untuk menampung foto bukti retur
    
    // Array Data
    public $detailTerima = [];
    public $listPengirimanDO = [];

    public function mount()
    {
        $this->tanggal_terima = date('Y-m-d');
    }

    // Trigger otomatis saat ID Pengiriman dipilih di dropdown
    public function updatedIdPengiriman($value)
    {
        $this->detailTerima = [];

        if ($value) {
            $do = Pengiriman::with('detailPengiriman.detailKontrak.material')->find($value);
            
            if ($do && $do->detailPengiriman) {
                foreach ($do->detailPengiriman as $det) {
                    $this->detailTerima[$det->id_pengiriman_detail] = [
                        'id_detail_kontrak' => $det->id_detail_kontrak,
                        'nama_material' => $det->detailKontrak->material->nama_material ?? 'Unknown',
                        'qty_dikirim' => $det->jumlah_dikirim,
                        'jumlah_bagus' => $det->jumlah_dikirim, // Default kondisi bagus semua
                        'jumlah_rusak' => 0,
                        'alasan_return' => ''
                    ];
                }
            }
        }
    }

    // Trigger otomatis ubah status jika ada input barang rusak
    public function updatedDetailTerima()
    {
        $adaRusak = false;
        foreach ($this->detailTerima as $item) {
            if ((int)$item['jumlah_rusak'] > 0) {
                $adaRusak = true;
                break;
            }
        }
        $this->status_penerimaan = $adaRusak ? 'Diterima Sebagian' : 'Diterima Penuh';
    }

    public function create()
    {
        $this->resetForm();
        // Hanya ambil DO yang statusnya sedang dalam perjalanan atau tiba
        $this->listPengirimanDO = Pengiriman::whereIn('status_pengiriman', ['Dalam Perjalanan', 'Tiba di Lokasi'])->get();
        $this->isModalOpen = true;
    }

    public function store()
    {
        // 1. Aturan Validasi Dasar
        $rules = [
            'id_pengiriman' => 'required',
            'tanggal_terima' => 'required|date',
            'status_penerimaan' => 'required',
            'detailTerima.*.jumlah_bagus' => 'required|numeric|min:0',
            'detailTerima.*.jumlah_rusak' => 'required|numeric|min:0',
        ];

        // 2. Tambahan Validasi: Wajib upload foto maksimal 2MB jika ada retur
        if ($this->status_penerimaan !== 'Diterima Penuh') {
            $rules['foto_bukti_rusak'] = 'required|image|max:2048';
        }

        $this->validate($rules);

        DB::transaction(function () {
            // 3. Proses Upload Foto (otomatis membuat folder jika belum ada)
            $fotoPath = null;
            if ($this->foto_bukti_rusak) {
                $fotoPath = $this->foto_bukti_rusak->store('penerimaan/bukti_retur', 'public');
            }

            // 4. Simpan Header Penerimaan ke Database
            $penerimaan = PenerimaanMaterial::create([
                'id_pengiriman' => $this->id_pengiriman,
                'id_user_penerima' => Auth::id() ?? 1,
                'tanggal_terima' => $this->tanggal_terima,
                'nomor_surat_jalan' => $this->nomor_surat_jalan,
                'status_penerimaan' => $this->status_penerimaan,
                'foto_bukti_rusak' => $fotoPath, // Simpan path foto ke kolom DB
            ]);

            // 5. Simpan Rincian Barang (Detail Penerimaan)
            foreach ($this->detailTerima as $idPengirimanDetail => $item) {
                DetailPenerimaan::create([
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'id_pengiriman_detail' => $idPengirimanDetail,
                    'id_detail_kontrak' => $item['id_detail_kontrak'],
                    'jumlah_bagus' => $item['jumlah_bagus'],
                    'jumlah_rusak' => $item['jumlah_rusak'],
                    'alasan_return' => $item['alasan_return'] ?? null,
                ]);
            }

            // ========================================================
            // 6. UPDATE STATUS PENGIRIMAN (DO) BERDASARKAN KONDISI
            // ========================================================
            $do = Pengiriman::find($this->id_pengiriman);
            if ($do) {
                if ($this->status_penerimaan === 'Diterima Penuh') {
                    // Jika aman semua, DO selesai
                    $do->update(['status_pengiriman' => 'Selesai']); 
                } else {
                    // Jika "Diterima Sebagian" atau "Return", DO diubah agar tim pengadaan tahu ada retur
                    $do->update(['status_pengiriman' => 'Return & Kirim Ulang']); 
                }
            }
        });

        session()->flash('message', 'Penerimaan Material Berhasil Disimpan!');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetForm()
    {
        $this->reset(['id_pengiriman', 'nomor_surat_jalan', 'detailTerima', 'foto_bukti_rusak']);
        $this->tanggal_terima = date('Y-m-d');
        $this->status_penerimaan = 'Diterima Penuh';
    }

    public function render()
    {
        return view('livewire.logistik.penerimaan.penerimaan-index', [
            'listPenerimaan' => PenerimaanMaterial::with(['pengiriman', 'user', 'detail_penerimaan.detail_kontrak.material'])
                ->orderBy('created_at', 'desc')
                ->where('id_penerimaan', 'like', "%{$this->search}%")
                ->paginate(10)
        ]);
    }
}