<?php

namespace App\Livewire\Logistik\Stok;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\StokBatchFifo;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class StokIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filter_kategori = ''; // Tambahkan ini

    // Tambahkan fungsi ini agar saat filter diganti, tabel kembali ke halaman 1
    public function updatingFilterKategori()
    {
        $this->resetPage();
    }


    // Variabel Modal Batch
    public $isModalBatchOpen = false;
    public $detailBatches = [];
    public $namaMaterialModal = '';
    public $kategoriMaterialModal = '';
    public $totalStokModal = 0;

    // Variabel Lapor Rusak
    public $isModalAdjustmentOpen = false;
    public $adj_id_stok, $adj_id_material, $adj_max_stok;
    public $adj_jumlah, $adj_jenis = 'Rusak', $adj_keterangan;
    public $adj_bukti_foto; 

    // Variabel Pindah Rak / Split Batch (DISESUAIKAN DENGAN MASTER LOKASI)
    public $isModalPindahRakOpen = false;
    public $pindah_id_stok, $pindah_id_material, $pindah_id_lokasi_asal, $pindah_id_lokasi_tujuan;
    public $pindah_max_stok, $pindah_jumlah;
    public $listLokasiRak = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }


    

    public function lihatBatch($id_material)
    {
        $this->detailBatches = StokBatchFifo::with(['lokasiRak', 'material.kategori'])
            ->where('id_material', $id_material)
            ->where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk', 'asc') 
            ->get();

        if($this->detailBatches->count() > 0) {
            $firstBatch = $this->detailBatches->first();
            $this->namaMaterialModal = $firstBatch->material->nama_material ?? 'Unknown';
            $this->kategoriMaterialModal = $firstBatch->material->kategori->nama_kategori ?? 'Umum';
            $this->totalStokModal = $this->detailBatches->sum('sisa_stok');
            
            $this->isModalBatchOpen = true;
        } else {
            $this->isModalBatchOpen = false;
        }
    }

    public function closeModal()
    {
        $this->isModalBatchOpen = false;
        $this->detailBatches = [];
    }

    // ==========================================
    // FITUR PINDAH RAK & SPLIT BATCH
    // ==========================================
    public function openPindahRak($id_stok, $id_material, $id_lokasi_asal, $sisa_stok)
    {
        $this->pindah_id_stok = $id_stok;
        $this->pindah_id_material = $id_material;
        $this->pindah_id_lokasi_asal = $id_lokasi_asal;
        $this->pindah_max_stok = $sisa_stok;
        $this->pindah_jumlah = $sisa_stok; // Default terisi full max
        $this->pindah_id_lokasi_tujuan = '';
        
        // Ambil data semua rak dari database master_lokasi_rak
        $this->listLokasiRak = DB::table('master_lokasi_rak')->get();
        $this->isModalPindahRakOpen = true;
    }

    public function closePindahRak()
    {
        $this->isModalPindahRakOpen = false;
        $this->resetValidation();
    }

    public function submitPindahRak()
    {
        $this->validate([
            'pindah_jumlah' => 'required|numeric|min:1|max:' . $this->pindah_max_stok,
            'pindah_id_lokasi_tujuan' => 'required|different:pindah_id_lokasi_asal',
        ], [
            'pindah_id_lokasi_tujuan.required' => 'Rak tujuan wajib dipilih.',
            'pindah_id_lokasi_tujuan.different' => 'Rak tujuan tidak boleh sama dengan rak asal.',
            'pindah_jumlah.max' => 'Jumlah yang dipindah melebihi stok yang ada.',
        ]);

        DB::beginTransaction();
        try {
            $batch = StokBatchFifo::where('id_stok', $this->pindah_id_stok)->first();
            
            if ($this->pindah_jumlah == $batch->sisa_stok) {
                // Skenario 1: PINDAH SEMUA
                $batch->id_lokasi = $this->pindah_id_lokasi_tujuan;
                $batch->save();
            } else {
                // Skenario 2: PINDAH SEBAGIAN (SPLIT BATCH)
                $batch->sisa_stok -= $this->pindah_jumlah;
                $batch->save();
                
                $newBatch = $batch->replicate();
                
                // MENGGUNAKAN IDE ANDA: Awalan STK agar urutan abjadnya tetap di bawah aslinya
                $newBatch->id_stok = 'STK' . date('ymdHis') . rand(10, 99); 
                
                $newBatch->id_lokasi = $this->pindah_id_lokasi_tujuan;
                $newBatch->jumlah_awal = $this->pindah_jumlah;
                $newBatch->sisa_stok = $this->pindah_jumlah;
                
                $newBatch->save();
            }

            DB::commit();
            session()->flash('success', 'Berhasil memindahkan/memecah stok ke lokasi tujuan.');
            
            $this->closePindahRak();
            $this->lihatBatch($this->pindah_id_material);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->closePindahRak(); 
            session()->flash('error', 'Gagal memindah rak: ' . $e->getMessage());
        }
    }
    // ==========================================
    // FITUR LAPOR RUSAK
    // ==========================================
    public function openAdjustment($id_stok, $id_material, $sisa_stok)
    {
        $this->adj_id_stok = $id_stok;
        $this->adj_id_material = $id_material;
        $this->adj_max_stok = $sisa_stok;
        $this->adj_jumlah = '';
        $this->adj_keterangan = '';
        $this->adj_jenis = 'Rusak';
        $this->adj_bukti_foto = null; 
        
        $this->isModalAdjustmentOpen = true;
    }

    public function closeAdjustment()
    {
        $this->isModalAdjustmentOpen = false;
        $this->adj_bukti_foto = null; 
        $this->resetValidation();
    }

    public function submitAdjustment()
    {
        $this->validate([
            'adj_jumlah' => 'required|numeric|min:1|max:' . $this->adj_max_stok,
            'adj_bukti_foto' => 'required|image|max:2048', // (Sesuaikan jika validasi foto Anda berbeda)
        ], [
            'adj_jumlah.required' => 'Jumlah penyesuaian wajib diisi.',
            'adj_jumlah.min' => 'Jumlah tidak boleh 0 atau minus.', // Tambahkan baris ini
            'adj_jumlah.max' => 'Jumlah melebihi sisa stok yang ada.',
            'adj_bukti_foto.required' => 'Bukti foto wajib diunggah.',
        ]);

        DB::beginTransaction();
        try {
            $fotoPath = null;
            if ($this->adj_bukti_foto) {
                $fotoPath = $this->adj_bukti_foto->store('bukti_penyesuaian', 'public');
            }

            $id_penyesuaian = 'ADJ-' . date('Ymd-His'); 
            
            DB::table('penyesuaian_stok')->insert([
                'id_penyesuaian' => $id_penyesuaian,
                'id_stok' => $this->adj_id_stok,
                'id_material' => $this->adj_id_material,
                'id_user' => Auth::user()->id_user ?? 'SYSTEM', 
                'jenis_penyesuaian' => $this->adj_jenis,
                'jumlah_penyesuaian' => $this->adj_jumlah,
                'keterangan' => $this->adj_keterangan,
                'bukti_foto' => $fotoPath, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $batch = StokBatchFifo::where('id_stok', $this->adj_id_stok)->first();
            $batch->sisa_stok -= $this->adj_jumlah;
            
            if ($batch->sisa_stok <= 0) {
                $batch->status_stok = 'Habis';
                $batch->sisa_stok = 0;
            }
            $batch->save();

            DB::commit();

            session()->flash('success', 'Laporan stok rusak/penyesuaian berhasil dicatat.');
            $this->closeAdjustment();
            $this->lihatBatch($this->adj_id_material);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // 1. Siapkan Query Dasar
        $query = Material::with('kategori')
            ->leftJoin('stok_batch_fifo', function($join) {
                $join->on('material.id_material', '=', 'stok_batch_fifo.id_material')
                     ->where('stok_batch_fifo.sisa_stok', '>', 0); 
            })
            ->select(
                'material.id_material',
                'material.nama_material',
                'material.id_kategori_material',
                DB::raw('COALESCE(SUM(stok_batch_fifo.sisa_stok), 0) as total_sisa'), 
                DB::raw('MIN(stok_batch_fifo.tanggal_masuk) as tgl_terlama'),
                DB::raw('COUNT(stok_batch_fifo.id_stok) as jumlah_batch')
            )
            ->where(function($q) {
                $q->where('material.nama_material', 'like', '%' . $this->search . '%')
                  ->orWhere('material.id_material', 'like', '%' . $this->search . '%');
            });

        // 2. Terapkan Filter Kategori (Jika user memilih salah satu kategori)
        if (!empty($this->filter_kategori)) {
            $query->where('material.id_kategori_material', $this->filter_kategori);
        }

        // 3. Eksekusi Query untuk Tabel
        $listStok = $query->groupBy('material.id_material', 'material.nama_material', 'material.id_kategori_material')
            ->orderBy('total_sisa', 'desc')
            ->paginate(10);

        // 4. Ambil data kategori untuk isi Dropdown Filter
        // Catatan: Sesuaikan 'kategori_material' dengan nama tabel kategori di database Anda jika berbeda
        $listKategori = DB::table('kategori_material')->get(); 

        return view('livewire.logistik.stok.stok-index', [
            'listStok' => $listStok,
            'listKategori' => $listKategori
        ]);
    }
}