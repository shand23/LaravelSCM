<?php

namespace App\Livewire\Pelaksanaan\PenggunaanMaterial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PenggunaanMaterial;
use App\Models\DetailPenggunaanMaterial;
use App\Models\PermintaanProyek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PenggunaanIndex extends Component
{
    use WithPagination;

    // State untuk UI
    public $search = '';
    public $isModalOpen = false;
    public $isModalDetailOpen = false;

    // Data untuk Form Input
    public $id_permintaan_selected = '';
    public $area_pekerjaan = '';
    public $tanggal_laporan = '';
    public $keterangan_umum = '';
    public $detailBarang = [];

    // Data untuk Lihat Detail
    public $laporanTerpilih = null;
    public $detailItems = [];

    public function mount()
    {
        $this->tanggal_laporan = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- LOGIKA MODAL INPUT ---
    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function resetForm()
    {
        $this->id_permintaan_selected = '';
        $this->area_pekerjaan = '';
        $this->keterangan_umum = '';
        $this->detailBarang = [];
        $this->tanggal_laporan = date('Y-m-d');
    }

    // Trigger saat dropdown permintaan dipilih
    public function updatedIdPermintaanSelected($id_permintaan)
    {
        $this->detailBarang = [];
        if ($id_permintaan) {
            $permintaan = PermintaanProyek::with('detailPermintaan.material')->find($id_permintaan);
            if ($permintaan) {
                foreach ($permintaan->detailPermintaan as $detail) {
                    $this->detailBarang[] = [
                        'id_material' => $detail->id_material,
                        'nama_material' => $detail->material->nama_material ?? 'Material Tidak Diketahui',
                        'jumlah_terkirim' => $detail->jumlah_terkirim,
                        // Set default awal terpasang sama dengan terkirim
                        'jumlah_terpasang_riil' => $detail->jumlah_terkirim, 
                        'jumlah_rusak_lapangan' => 0,
                        'jumlah_sisa_material' => 0,
                        'catatan_khusus' => ''
                    ];
                }
            }
        }
    }

    // Trigger otomatis saat inputan di dalam array detailBarang berubah (rusak/sisa)
 // Trigger otomatis saat inputan di dalam array detailBarang berubah (rusak/sisa)
    public function updatedDetailBarang($value, $key)
    {
        $parts = explode('.', $key);
        
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if (in_array($field, ['jumlah_rusak_lapangan', 'jumlah_sisa_material'])) {
                // 1. VALIDASI: Jika user input angka minus, otomatis kembalikan ke 0
                if ($value < 0 || $value === '') {
                    $this->detailBarang[$index][$field] = 0;
                }

                $terkirim = (float) ($this->detailBarang[$index]['jumlah_terkirim'] ?? 0);
                $rusak = (float) ($this->detailBarang[$index]['jumlah_rusak_lapangan'] ?: 0);
                $sisa = (float) ($this->detailBarang[$index]['jumlah_sisa_material'] ?: 0);

                // Kalkulasi: Terpasang = Terkirim - Rusak - Sisa
                $terpasang = $terkirim - $rusak - $sisa;
                
                // Pastikan hasilnya tidak minus jika jumlah rusak + sisa melebihi terkirim
                $this->detailBarang[$index]['jumlah_terpasang_riil'] = max(0, $terpasang);
            }
        }
    }

    public function simpanLaporan()
    {
        // 2. VALIDASI BACKEND: Tambahkan aturan min:0 untuk rusak dan sisa
        $this->validate([
            'id_permintaan_selected' => 'required',
            'tanggal_laporan' => 'required|date',
            'area_pekerjaan' => 'required|string|max:150',
            'detailBarang.*.jumlah_terpasang_riil' => 'required|numeric|min:0',
            'detailBarang.*.jumlah_rusak_lapangan' => 'required|numeric|min:0',
            'detailBarang.*.jumlah_sisa_material' => 'required|numeric|min:0',
        ], [
            'detailBarang.*.*.min' => 'Input jumlah tidak boleh bernilai minus.'
        ]);

        // ... Lanjutan kode try-catch DB::transaction() sama seperti sebelumnya ...
        try {
            DB::transaction(function () {
                $permintaan = PermintaanProyek::find($this->id_permintaan_selected);
                
                // Generate ID: USE-YYYYMMDD-XXX
                $prefix = 'USE-' . date('Ymd');
                $last = PenggunaanMaterial::where('id_penggunaan', 'like', $prefix . '%')->orderBy('id_penggunaan', 'desc')->first();
                $seq = $last ? (int)substr($last->id_penggunaan, -3) + 1 : 1;
                $id_penggunaan = $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

                PenggunaanMaterial::create([
                    'id_penggunaan' => $id_penggunaan,
                    'id_permintaan' => $this->id_permintaan_selected,
                    'id_proyek' => $permintaan->id_proyek,
                    'id_user_pelaksana' => Auth::id(),
                    'tanggal_laporan' => $this->tanggal_laporan,
                    'area_pekerjaan' => $this->area_pekerjaan,
                    'keterangan_umum' => $this->keterangan_umum,
                ]);

                foreach ($this->detailBarang as $index => $item) {
                    DetailPenggunaanMaterial::create([
                        'id_detail_penggunaan' => $id_penggunaan . '-D' . ($index + 1),
                        'id_penggunaan' => $id_penggunaan,
                        'id_material' => $item['id_material'],
                        'jumlah_terpasang_riil' => $item['jumlah_terpasang_riil'],
                        'jumlah_rusak_lapangan' => $item['jumlah_rusak_lapangan'],
                        'jumlah_sisa_material' => $item['jumlah_sisa_material'],
                        'catatan_khusus' => $item['catatan_khusus']
                    ]);
                }
            });

            session()->flash('message', 'Laporan Berhasil Disimpan!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- LOGIKA MODAL DETAIL ---
    public function bukaDetail($id)
    {
        $this->laporanTerpilih = PenggunaanMaterial::with(['proyek', 'permintaan', 'pelaksana'])->find($id);
        if ($this->laporanTerpilih) {
            $this->detailItems = DetailPenggunaanMaterial::with('material')->where('id_penggunaan', $id)->get();
            $this->isModalDetailOpen = true;
        }
    }

    public function tutupDetail()
    {
        $this->isModalDetailOpen = false;
        $this->laporanTerpilih = null;
    }

    public function render()
    {
        // 1. Ambil semua ID Permintaan yang sudah pernah dilaporkan (digunakan)
        $usedPermintaanIds = PenggunaanMaterial::pluck('id_permintaan')->toArray();

        return view('livewire.pelaksanaan.penggunaan-material.penggunaan-index', [
            'listLaporan' => PenggunaanMaterial::with(['proyek'])
                ->where('id_penggunaan', 'like', '%' . $this->search . '%')
                ->orWhere('area_pekerjaan', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            
            // 2. Filter data permintaan, hanya tampilkan yang ID-nya tidak ada di array $usedPermintaanIds
            'daftarPermintaan' => PermintaanProyek::whereIn('status_permintaan', ['Diproses Sebagian', 'Selesai'])
                ->whereNotIn('id_permintaan', $usedPermintaanIds) 
                ->get()
        ]);
    }
}