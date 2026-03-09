<?php

namespace App\Livewire\Logistik\PengajuanPembelian;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PengajuanPembelian;
use App\Models\DetailPengajuanPembelian;
use App\Models\PermintaanProyek;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PengajuanIndex extends Component
{
    use WithPagination;

    // Properti UI
    public $search = '';
    public $isModalOpen = false;
    public $isDetailOpen = false;
    public $isEditMode = false;
    public $edit_id = null;

    // Properti Form Utama
    public $referensi_id_permintaan;
    public $tanggal_pengajuan;
    public $items = []; 
    public $selectedPengajuan;

    // Properti Validasi Tanggal Dinamis
    public $min_date;
    public $max_date;

    // Properti Form Manual (Restock Murni)
    public $listMaterial = [];
    public $selected_material_id = '';
    public $jumlah_manual = 1;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->tanggal_pengajuan = date('Y-m-d');
        $this->min_date = date('Y-m-d'); // Default untuk restok murni
        $this->max_date = null;
    }

    public function create()
    {
        $this->reset(['referensi_id_permintaan', 'items', 'edit_id', 'selected_material_id', 'max_date']);
        $this->jumlah_manual = 1;
        $this->isEditMode = false;
        $this->tanggal_pengajuan = date('Y-m-d');
        $this->min_date = date('Y-m-d'); // Kunci di hari ini untuk restok
        
        $this->listMaterial = Material::orderBy('nama_material', 'asc')->get();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $pengajuan = PengajuanPembelian::with('detailPengajuan.material')->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'Menunggu Pengadaan') {
            session()->flash('error', 'Data tidak bisa diedit karena sudah diproses oleh Pengadaan.');
            return;
        }

        $this->isEditMode = true;
        $this->edit_id = $id;
        $this->referensi_id_permintaan = $pengajuan->referensi_id_permintaan;
        $this->tanggal_pengajuan = $pengajuan->tanggal_pengajuan;
        
        // Setup batas tanggal untuk mode edit
        if ($this->referensi_id_permintaan) {
            $permintaan = PermintaanProyek::find($this->referensi_id_permintaan);
            if ($permintaan) {
                $this->min_date = $permintaan->tanggal_permintaan;
                
                $penugasan = DB::table('penugasan_proyek')
                    ->where('id_proyek', $permintaan->id_proyek)
                    ->where('id_user', $permintaan->id_user_mandor)
                    ->orderBy('tanggal_selesai', 'desc')
                    ->first();
                    
                $this->max_date = $penugasan ? $penugasan->tanggal_selesai : null;
            }
        } else {
            // Untuk restok gudang saat edit, min date diset ke tanggal pengajuan sebelumnya agar tidak error
            $this->min_date = min(date('Y-m-d'), $pengajuan->tanggal_pengajuan);
            $this->max_date = null;
        }
        
        $this->listMaterial = Material::orderBy('nama_material', 'asc')->get();

        $this->items = [];
        foreach ($pengajuan->detailPengajuan as $detail) {
            $this->items[] = [
                'id_material' => $detail->id_material,
                'nama_material' => $detail->material->nama_material,
                'satuan' => $detail->material->satuan,
                'jumlah_minta_beli' => $detail->jumlah_minta_beli,
            ];
        }

        $this->isModalOpen = true;
    }

    // Fungsi otomatis menarik detail material & setup batas tanggal saat proyek dipilih
    public function updatedReferensiIdPermintaan($id)
    {
        $this->items = []; 
        $this->resetErrorBag('items');
        
        if ($id) {
            $permintaan = PermintaanProyek::with('detailPermintaan.material')->find($id);
            
            if ($permintaan) {
                // Set batas minimal tanggal = tanggal permintaan mandor
                $this->min_date = $permintaan->tanggal_permintaan;
                
                // Cari tanggal selesai proyek dari tabel penugasan
                $penugasan = DB::table('penugasan_proyek')
                    ->where('id_proyek', $permintaan->id_proyek)
                    ->where('id_user', $permintaan->id_user_mandor)
                    ->orderBy('tanggal_selesai', 'desc')
                    ->first();
                    
                $this->max_date = $penugasan ? $penugasan->tanggal_selesai : null;

                // Auto-koreksi tanggal jika di luar batas
                if ($this->tanggal_pengajuan < $this->min_date) {
                    $this->tanggal_pengajuan = $this->min_date;
                }
                if ($this->max_date && $this->tanggal_pengajuan > $this->max_date) {
                    $this->tanggal_pengajuan = $this->max_date;
                }

                foreach ($permintaan->detailPermintaan as $detail) {
                    $this->items[] = [
                        'id_material' => $detail->id_material,
                        'nama_material' => $detail->material->nama_material,
                        'satuan' => $detail->material->satuan,
                        'jumlah_minta_beli' => $detail->jumlah_diminta, 
                    ];
                }
            }
        } else {
            // Jika dikosongkan (kembali ke restok murni)
            $this->min_date = date('Y-m-d');
            $this->max_date = null;
            if ($this->tanggal_pengajuan < $this->min_date) {
                $this->tanggal_pengajuan = $this->min_date;
            }
        }
    }

    public function addManualItem()
    {
        $this->validate([
            'selected_material_id' => 'required',
            'jumlah_manual' => 'required|numeric|min:1',
        ], [
            'selected_material_id.required' => 'Pilih material terlebih dahulu.',
            'jumlah_manual.min' => 'Jumlah minimal 1.',
        ]);

        $material = Material::find($this->selected_material_id);
        
        if ($material) {
            $exists = collect($this->items)->firstWhere('id_material', $material->id_material);
            
            if ($exists) {
                $this->addError('selected_material_id', 'Material ini sudah ada di daftar.');
                return;
            }

            $this->items[] = [
                'id_material' => $material->id_material,
                'nama_material' => $material->nama_material,
                'satuan' => $material->satuan,
                'jumlah_minta_beli' => $this->jumlah_manual,
            ];

            $this->reset('selected_material_id');
            $this->jumlah_manual = 1;
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); 
    }

    public function delete($id)
    {
        $pengajuan = PengajuanPembelian::findOrFail($id);
        if ($pengajuan->status_pengajuan === 'Menunggu Pengadaan') {
            DB::transaction(function () use ($pengajuan) {
                if ($pengajuan->referensi_id_permintaan) {
                    PermintaanProyek::where('id_permintaan', $pengajuan->referensi_id_permintaan)
                        ->update(['status_permintaan' => 'Disetujui PM']); 
                }
                $pengajuan->delete();
            });
            session()->flash('message', 'Pengajuan Pembelian (PR) berhasil dibatalkan.');
        } else {
            session()->flash('error', 'PR tidak dapat dihapus karena sudah diproses.');
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isDetailOpen = false;
        $this->resetErrorBag();
    }

    public function show($id)
    {
        $this->selectedPengajuan = PengajuanPembelian::with(['detailPengajuan.material', 'permintaanProyek.proyek', 'user'])->findOrFail($id);
        $this->isDetailOpen = true;
    }

    public function store()
    {
        // Setup aturan validasi tanggal
        $dateRule = 'required|date';
        
        if ($this->referensi_id_permintaan) {
            $dateRule .= '|after_or_equal:' . $this->min_date;
            if ($this->max_date) {
                $dateRule .= '|before_or_equal:' . $this->max_date;
            }
        } else {
            // Restok murni: Untuk create tidak boleh kurang dari hari ini.
            if (!$this->isEditMode) {
                $dateRule .= '|after_or_equal:today';
            }
        }

        $this->validate([
            'tanggal_pengajuan' => $dateRule,
            'items.*.id_material' => 'required',
            'items.*.jumlah_minta_beli' => 'required|numeric|min:1',
        ], [
            'tanggal_pengajuan.after_or_equal' => 'Tanggal pengajuan tidak boleh kurang dari batas minimal (' . date('d M Y', strtotime($this->min_date)) . ').',
            'tanggal_pengajuan.before_or_equal' => 'Tanggal pengajuan tidak boleh melebihi batas selesai proyek (' . ($this->max_date ? date('d M Y', strtotime($this->max_date)) : '') . ').',
            'items.*.jumlah_minta_beli.min' => 'Jumlah minimal 1.',
        ]);

        if (empty($this->items)) {
            $this->addError('referensi_id_permintaan', 'Daftar material tidak boleh kosong. Tambahkan material jika ini restok murni.');
            return;
        }

        DB::transaction(function () {
            if ($this->isEditMode) {
                $header = PengajuanPembelian::findOrFail($this->edit_id);
                $header->update([
                    'tanggal_pengajuan' => $this->tanggal_pengajuan,
                ]);

                DetailPengajuanPembelian::where('id_pengajuan', $this->edit_id)->delete();

                foreach ($this->items as $item) {
                    DetailPengajuanPembelian::create([
                        'id_pengajuan' => $header->id_pengajuan,
                        'id_material' => $item['id_material'],
                        'jumlah_minta_beli' => $item['jumlah_minta_beli'],
                    ]);
                }
                session()->flash('message', 'Pengajuan Pembelian (PR) berhasil diperbarui!');
            } else {
                $header = PengajuanPembelian::create([
                    'id_user_logistik' => Auth::id(),
                    'referensi_id_permintaan' => $this->referensi_id_permintaan ?: null,
                    'tanggal_pengajuan' => $this->tanggal_pengajuan,
                    'status_pengajuan' => 'Menunggu Pengadaan',
                ]);

                foreach ($this->items as $item) {
                    DetailPengajuanPembelian::create([
                        'id_pengajuan' => $header->id_pengajuan, 
                        'id_material' => $item['id_material'],
                        'jumlah_minta_beli' => $item['jumlah_minta_beli'],
                    ]);
                }

                if ($this->referensi_id_permintaan) {
                    PermintaanProyek::where('id_permintaan', $this->referensi_id_permintaan)
                        ->update(['status_permintaan' => 'Diproses Sebagian']); 
                }

                session()->flash('message', 'Pengajuan Pembelian (PR) berhasil dibuat dan diteruskan ke Pengadaan!');
            }
        });

        $this->closeModal();
    }

    public function render()
    {
        $pengajuans = PengajuanPembelian::with(['permintaanProyek.proyek'])
            ->where(function($q) {
                $q->where('id_pengajuan', 'like', "%{$this->search}%")
                  ->orWhere('referensi_id_permintaan', 'like', "%{$this->search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $listPermintaanMasuk = PermintaanProyek::with('proyek')
            ->whereIn('status_permintaan', ['Menunggu Persetujuan', 'Disetujui PM'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.logistik.pengajuan-pembelian.pengajuan-index', [
            'pengajuans' => $pengajuans,
            'listPermintaanMasuk' => $listPermintaanMasuk,
        ]);
    }
}