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

    public $search = '';
    public $isModalOpen = false;

    // Form Properties
    public $id_permintaan_selected = '';
    public $area_pekerjaan = '';
    public $tanggal_laporan = '';
    public $keterangan_umum = '';
    
    // Array untuk menampung inputan detail dari lapangan
    public $detailBarang = [];

    public function mount()
    {
        $this->tanggal_laporan = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->id_permintaan_selected = '';
        $this->area_pekerjaan = '';
        $this->keterangan_umum = '';
        $this->detailBarang = [];
        $this->tanggal_laporan = date('Y-m-d');
    }

    // Fungsi ini dipanggil otomatis oleh Livewire saat dropdown Permintaan dipilih
    public function updatedIdPermintaanSelected($id_permintaan)
    {
        $this->detailBarang = []; // Reset dulu

        if ($id_permintaan) {
            $permintaan = PermintaanProyek::with('detailPermintaan.material')->find($id_permintaan);
            
            if ($permintaan) {
                // Looping barang yang diminta untuk dijadikan form isian
                foreach ($permintaan->detailPermintaan as $detail) {
                    $this->detailBarang[] = [
                        'id_material' => $detail->id_material,
                        'nama_material' => $detail->material->nama_material ?? 'Unknown',
                        'jumlah_terkirim' => $detail->jumlah_terkirim, // Sebagai informasi referensi
                        'jumlah_terpasang_riil' => 0,
                        'jumlah_rusak_lapangan' => 0,
                        'jumlah_sisa_material' => 0,
                        'catatan_khusus' => ''
                    ];
                }
            }
        }
    }

    public function simpanLaporan()
    {
        $this->validate([
            'id_permintaan_selected' => 'required',
            'tanggal_laporan' => 'required|date',
            'area_pekerjaan' => 'required|string|max:150',
            'detailBarang.*.jumlah_terpasang_riil' => 'required|numeric|min:0',
            'detailBarang.*.jumlah_rusak_lapangan' => 'required|numeric|min:0',
            'detailBarang.*.jumlah_sisa_material' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            // Ambil data proyek dari permintaan
            $permintaan = PermintaanProyek::find($this->id_permintaan_selected);
            
            // Generate ID Penggunaan (Format: USE-YYYYMMDD-XXX)
            $datePrefix = date('Ymd');
            $lastPenggunaan = PenggunaanMaterial::where('id_penggunaan', 'like', 'USE-' . $datePrefix . '-%')
                                                ->orderBy('id_penggunaan', 'desc')->first();
            $urut = $lastPenggunaan ? (int)substr($lastPenggunaan->id_penggunaan, -3) + 1 : 1;
            $id_penggunaan = 'USE-' . $datePrefix . '-' . str_pad($urut, 3, '0', STR_PAD_LEFT);

            // 1. Simpan Header Penggunaan
            PenggunaanMaterial::create([
                'id_penggunaan' => $id_penggunaan,
                'id_permintaan' => $this->id_permintaan_selected,
                'id_proyek' => $permintaan->id_proyek,
                'id_user_pelaksana' => Auth::user()->id_user ?? Auth::id(), // Sesuaikan dengan field user Anda
                'tanggal_laporan' => $this->tanggal_laporan,
                'area_pekerjaan' => $this->area_pekerjaan,
                'keterangan_umum' => $this->keterangan_umum,
            ]);

            // 2. Simpan Detail Penggunaan
            foreach ($this->detailBarang as $index => $item) {
                $id_detail = $id_penggunaan . '-D' . ($index + 1);

                DetailPenggunaanMaterial::create([
                    'id_detail_penggunaan' => $id_detail,
                    'id_penggunaan' => $id_penggunaan,
                    'id_material' => $item['id_material'],
                    'jumlah_terpasang_riil' => $item['jumlah_terpasang_riil'],
                    'jumlah_rusak_lapangan' => $item['jumlah_rusak_lapangan'],
                    'jumlah_sisa_material' => $item['jumlah_sisa_material'],
                    'catatan_khusus' => $item['catatan_khusus'] ?? null,
                ]);
            }
        });

        session()->flash('message', 'Laporan Penggunaan Material berhasil disimpan!');
        $this->closeModal();
    }

    public function render()
    {
        // Tampilkan daftar permintaan yang sudah diproses sebagian atau selesai untuk dipilih di dropdown
        $daftarPermintaan = PermintaanProyek::whereIn('status_permintaan', ['Diproses Sebagian', 'Selesai'])->get();

        // Tampilkan daftar laporan penggunaan di tabel utama
        $listLaporan = PenggunaanMaterial::with(['proyek', 'permintaan'])
            ->where('id_penggunaan', 'like', '%' . $this->search . '%')
            ->orWhere('area_pekerjaan', 'like', '%' . $this->search . '%')
            ->latest('tanggal_laporan')
            ->paginate(10);

        return view('livewire.pelaksanaan.penggunaan-material.penggunaan-index', [
            'listLaporan' => $listLaporan,
            'daftarPermintaan' => $daftarPermintaan
        ]);
    }
}