<?php

namespace App\Livewire\Manajemen\PenggunaanMaterial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PenggunaanMaterial;
use App\Models\DetailPenggunaanMaterial;
use App\Models\Proyek;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class PenggunaanManajemenIndex extends Component
{
    use WithPagination;

    // Filter utama
    public $search = '';
    public $filterProyek = '';          // ID proyek (kosong = semua)
    public $periode = 'semua';          // 'semua', 'bulan_ini', '3_bulan', '6_bulan', 'custom'
    public $customStart = '';
    public $customEnd = '';

    // Tanggal hasil filter (otomatis diisi berdasarkan periode)
    public $tanggalMulai = null;
    public $tanggalSelesai = null;

    // State modal detail
    public $isModalDetailOpen = false;
    public $laporanTerpilih = null;
    public $detailItems = [];

    // Reset pagination saat filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProyek() { $this->resetPage(); }
    public function updatingPeriode() { $this->resetPage(); $this->updateTanggalFilter(); }
    public function updatingCustomStart() { $this->resetPage(); $this->updateTanggalFilter(); }
    public function updatingCustomEnd() { $this->resetPage(); $this->updateTanggalFilter(); }

    // Method untuk mengisi $tanggalMulai dan $tanggalSelesai berdasarkan periode
    public function updateTanggalFilter()
    {
        if ($this->periode == 'custom') {
            $this->tanggalMulai = $this->customStart ?: null;
            $this->tanggalSelesai = $this->customEnd ?: null;
        } else {
            switch ($this->periode) {
                case 'bulan_ini':
                    $this->tanggalMulai = now()->startOfMonth()->toDateString();
                    $this->tanggalSelesai = now()->endOfMonth()->toDateString();
                    break;
                case '3_bulan':
                    $this->tanggalMulai = now()->subMonths(3)->startOfMonth()->toDateString();
                    $this->tanggalSelesai = now()->toDateString();
                    break;
                case '6_bulan':
                    $this->tanggalMulai = now()->subMonths(6)->startOfMonth()->toDateString();
                    $this->tanggalSelesai = now()->toDateString();
                    break;
                default: // 'semua'
                    $this->tanggalMulai = null;
                    $this->tanggalSelesai = null;
                    break;
            }
        }
    }

    // --- MODAL DETAIL ---
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
        $this->detailItems = [];
    }

    // --- EKSPOR PDF (menggunakan filter yang sama) ---
  // === EKSPOR PDF (DENGAN PENANGANAN ERROR) ===
public function exportPdf()
{
    try {
        // Gunakan query yang sama persis dengan render untuk mengambil data laporan
        $query = PenggunaanMaterial::with(['proyek', 'pelaksana', 'permintaan'])
            ->where(function($q) {
                $q->where('id_penggunaan', 'like', '%' . $this->search . '%')
                  ->orWhere('area_pekerjaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', fn($sq) => $sq->where('nama_proyek', 'like', '%' . $this->search . '%'));
            });

        if ($this->filterProyek) $query->where('id_proyek', $this->filterProyek);
        if ($this->tanggalMulai) $query->whereDate('tanggal_laporan', '>=', $this->tanggalMulai);
        if ($this->tanggalSelesai) $query->whereDate('tanggal_laporan', '<=', $this->tanggalSelesai);

        $listLaporan = $query->get();
        $ids = $listLaporan->pluck('id_penggunaan')->toArray();

        // Statistik menggunakan nama kolom yang benar
        $totalTerpasang = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_terpasang_riil');
        $totalRusak      = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_rusak_lapangan');
        $totalSisa       = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_sisa_material');
        $jumlahProyekAktif = Proyek::where('status_proyek', 'Aktif')->count();

        // Tren bulanan
        $trenBulan = DetailPenggunaanMaterial::selectRaw(
                "DATE_FORMAT(penggunaan_material.tanggal_laporan, '%b %Y') as bulan,
                 SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang,
                 SUM(detail_penggunaan_material.jumlah_rusak_lapangan) as total_rusak,
                 SUM(detail_penggunaan_material.jumlah_sisa_material) as total_sisa"
            )
            ->join('penggunaan_material', 'detail_penggunaan_material.id_penggunaan', '=', 'penggunaan_material.id_penggunaan')
            ->whereIn('detail_penggunaan_material.id_penggunaan', $ids)
            ->groupBy('bulan')
            ->orderBy(DB::raw("MIN(penggunaan_material.tanggal_laporan)"))
            ->get();

        // Top 5 kategori
        $kategoriData = DetailPenggunaanMaterial::selectRaw(
                'kategori_material.nama_kategori,
                 SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang'
            )
            ->join('material', 'detail_penggunaan_material.id_material', '=', 'material.id_material')
            ->join('kategori_material', 'material.id_kategori_material', '=', 'kategori_material.id_kategori_material')
            ->whereIn('detail_penggunaan_material.id_penggunaan', $ids)
            ->groupBy('kategori_material.nama_kategori')
            ->orderByDesc('total_terpasang')
            ->limit(5)
            ->get();

        $data = [
            'listLaporan'      => $listLaporan,
            'totalTerpasang'   => $totalTerpasang,
            'totalRusak'       => $totalRusak,
            'totalSisa'        => $totalSisa,
            'jumlahProyekAktif'=> $jumlahProyekAktif,
            'trenBulan'        => $trenBulan,
            'kategoriData'     => $kategoriData,
            'filterProyek'     => $this->filterProyek,
            'filterStartDate'  => $this->tanggalMulai,
            'filterEndDate'    => $this->tanggalSelesai,
            'search'           => $this->search,
        ];

        // Load view PDF
        $pdf = Pdf::loadView('livewire.manajemen.penggunaan-material.pdf-laporan-penggunaan', $data)
                  ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'Laporan_Penggunaan_Material_' . now()->format('Ymd_His') . '.pdf'
        );

    } catch (\Exception $e) {
        // Jika error, tampilkan pesan error di browser (untuk debugging)
        return response()->json([
            'error' => $e->getMessage(),
            'line'  => $e->getLine(),
            'file'  => $e->getFile()
        ], 500);
    }
}
   

    // Query dasar dengan filter (untuk tabel dan agregat)
    private function buildQuery()
    {
        $query = PenggunaanMaterial::with(['proyek', 'pelaksana', 'permintaan']);

        // Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->where('id_penggunaan', 'like', '%' . $this->search . '%')
                  ->orWhere('area_pekerjaan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('proyek', fn($sq) => $sq->where('nama_proyek', 'like', '%' . $this->search . '%'));
            });
        }

        // Filter proyek
        if ($this->filterProyek) {
            $query->where('id_proyek', $this->filterProyek);
        }

        // Filter tanggal (periode)
        if ($this->tanggalMulai) {
            $query->whereDate('tanggal_laporan', '>=', $this->tanggalMulai);
        }
        if ($this->tanggalSelesai) {
            $query->whereDate('tanggal_laporan', '<=', $this->tanggalSelesai);
        }

        return $query;
    }

    // === RENDER (untuk tampilan web) ===
    public function render()
    {
        // Pastikan tanggal filter terisi (saat pertama load)
        if ($this->periode !== 'custom' && !$this->tanggalMulai && $this->periode !== 'semua') {
            $this->updateTanggalFilter();
        }

        // Query untuk tabel (dengan pagination)
        $query = $this->buildQuery();
        $listLaporan = $query->latest('tanggal_laporan')->paginate(15);

        // Ambil ID penggunaan yang tampil di halaman (pagination) untuk statistik dan grafik
        $ids = $listLaporan->pluck('id_penggunaan')->toArray();

        // Statistik (berdasarkan data yang tampil di halaman, bukan seluruh database)
        $totalTerpasang = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_terpasang_riil');
        $totalRusak = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_rusak_lapangan');
        $totalSisa = DetailPenggunaanMaterial::whereIn('id_penggunaan', $ids)->sum('jumlah_sisa_material');
        $jumlahProyekAktif = Proyek::where('status_proyek', 'Aktif')->count();

        // Grafik tren (line) berdasarkan data yang ditampilkan (bisa lebih kecil dari pagination, kita pakai query tanpa pagination untuk grafik yang konsisten dengan filter)
        $queryGrafik = $this->buildQuery();
        $trenBulan = (clone $queryGrafik)
            ->join('detail_penggunaan_material', 'penggunaan_material.id_penggunaan', '=', 'detail_penggunaan_material.id_penggunaan')
            ->selectRaw("DATE_FORMAT(penggunaan_material.tanggal_laporan, '%b %Y') as bulan,
                         SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang,
                         SUM(detail_penggunaan_material.jumlah_rusak_lapangan) as total_rusak,
                         SUM(detail_penggunaan_material.jumlah_sisa_material) as total_sisa")
            ->groupBy('bulan')
            ->orderBy(DB::raw("MIN(penggunaan_material.tanggal_laporan)"))
            ->get();

        $labelsTren = $trenBulan->pluck('bulan');
        $dataTerpasang = $trenBulan->pluck('total_terpasang');
        $dataRusak = $trenBulan->pluck('total_rusak');
        $dataSisa = $trenBulan->pluck('total_sisa');

        // Grafik kategori (bar) dengan filter yang sama
        $kategoriData = (clone $queryGrafik)
            ->join('detail_penggunaan_material', 'penggunaan_material.id_penggunaan', '=', 'detail_penggunaan_material.id_penggunaan')
            ->join('material', 'detail_penggunaan_material.id_material', '=', 'material.id_material')
            ->join('kategori_material', 'material.id_kategori_material', '=', 'kategori_material.id_kategori_material')
            ->selectRaw('kategori_material.nama_kategori, SUM(detail_penggunaan_material.jumlah_terpasang_riil) as total_terpasang')
            ->groupBy('kategori_material.nama_kategori')
            ->orderByDesc('total_terpasang')
            ->limit(5)
            ->get();

        $labelsKategori = $kategoriData->pluck('nama_kategori');
        $dataKategori = $kategoriData->pluck('total_terpasang');

        // Daftar proyek untuk dropdown filter proyek
        $listProyek = Proyek::orderBy('nama_proyek')->get();

        return view('livewire.manajemen.penggunaan-material.penggunaan-manajemen-index', [
            'listLaporan' => $listLaporan,
            'listProyek' => $listProyek,
            'totalTerpasang' => $totalTerpasang,
            'totalRusak' => $totalRusak,
            'totalSisa' => $totalSisa,
            'jumlahProyekAktif' => $jumlahProyekAktif,
            'labelsTren' => $labelsTren,
            'dataTerpasang' => $dataTerpasang,
            'dataRusak' => $dataRusak,
            'dataSisa' => $dataSisa,
            'labelsKategori' => $labelsKategori,
            'dataKategori' => $dataKategori,
        ]);
    }
}