<?php

namespace App\Livewire\Pelaksanaan\PermintaanProyek;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PermintaanProyek;
use App\Models\DetailPermintaanProyek;
use App\Models\Proyek;
use App\Models\Material;
use App\Models\PenggunaanMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class IndexPermintaan extends Component
{
    use WithPagination;

    // Properti UI
    public $search = '';
    public $filterStatus = '';
    public $filterProyek = '';
    public $isModalOpen = false;
    public $isDetailOpen = false;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    // Properti Form & Edit
    public $id_proyek, $tanggal_permintaan, $items = [];
    public $selectedPermintaan;
    public $batas_tanggal = null;
    public $isEditMode = false;
    public $edit_id = null;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->tanggal_permintaan = date('Y-m-d');
        $this->resetItems();
    }

    public function resetItems()
    {
        $this->items = [['id_material' => '', 'jumlah_diminta' => 1]];
    }

    public function create()
    {
        $this->reset(['id_proyek', 'batas_tanggal', 'edit_id']);
        $this->isEditMode = false;
        $this->tanggal_permintaan = date('Y-m-d'); 
        $this->resetItems();
        $this->isModalOpen = true;
    }



    public function edit($id)
    {
        $this->resetErrorBag();
        $permintaan = PermintaanProyek::with('detailPermintaan')->findOrFail($id);
        
        // Keamanan ekstra: cegah edit jika status sudah berubah
       if (!in_array($permintaan->status_permintaan, ['Menunggu Persetujuan', 'Ditolak'])) {
            session()->flash('error', 'Data tidak bisa diedit karena status sudah diproses.');
            return;
        }

        $this->isEditMode = true;
        $this->edit_id = $id;
        $this->id_proyek = $permintaan->id_proyek;
        $this->tanggal_permintaan = $permintaan->tanggal_permintaan;
        
        $this->items = [];
        foreach ($permintaan->detailPermintaan as $detail) {
            $this->items[] = [
                'id_material' => $detail->id_material,
                'jumlah_diminta' => $detail->jumlah_diminta,
            ];
        }

        // Panggil fungsi untuk set batas maksimal tanggal
        $this->updatedIdProyek($this->id_proyek);
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $permintaan = PermintaanProyek::findOrFail($id);
        
        // PERBAIKAN: Izinkan penghapusan jika status Menunggu Persetujuan ATAU Ditolak
        if (in_array($permintaan->status_permintaan, ['Menunggu Persetujuan', 'Ditolak'])) {
            $permintaan->delete(); // Pastikan relasi di DB menggunakan ON DELETE CASCADE
            session()->flash('message', 'Permintaan material berhasil dihapus.');
        } else {
            session()->flash('error', 'Permintaan tidak dapat dihapus karena sedang diproses atau sudah selesai.');
        }
    }

    public function revisiPermintaan($id)
    {
        // Tutup modal detail
        $this->isDetailOpen = false;
        
        // Panggil fungsi edit untuk membuka form revisi
        $this->edit($id); 
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isDetailOpen = false;
        $this->resetErrorBag();
    }

    public function updatedIdProyek($id)
    {
        if ($id) {
            $penugasan = DB::table('penugasan_proyek')
                ->where('id_proyek', $id)
                ->where('id_user', Auth::id())
                ->where('status_penugasan', 'Aktif')
                ->first();

            $this->batas_tanggal = $penugasan ? $penugasan->tanggal_selesai : null;

            if ($this->batas_tanggal && $this->tanggal_permintaan > $this->batas_tanggal) {
                $this->tanggal_permintaan = $this->batas_tanggal;
            }
        } else {
            $this->batas_tanggal = null;
        }
    }

    public function addItem() 
    { 
        $this->items[] = ['id_material' => '', 'jumlah_diminta' => 1]; 
    }
    
    public function removeItem($index) 
    { 
        unset($this->items[$index]); 
        $this->items = array_values($this->items); 
    }

    public function show($id)
    {
        $this->selectedPermintaan = PermintaanProyek::with(['detailPermintaan.material', 'proyek'])
            ->findOrFail($id);
        $this->isDetailOpen = true;
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

  public function store()
    {
        $rules = [
            'id_proyek' => 'required',
            'items.*.id_material' => 'required',
            'items.*.jumlah_diminta' => 'required|numeric|min:1', 
        ];

        $messages = [
            'id_proyek.required' => 'Proyek harus dipilih.',
            'items.*.id_material.required' => 'Material harus dipilih.',
            'items.*.jumlah_diminta.min' => 'Jumlah minimal adalah 1.',
            'tanggal_permintaan.after_or_equal' => 'Tanggal permintaan tidak boleh kurang dari hari ini.',
        ];

        // Jika mode tambah, pastikan tanggal tidak kurang dari hari ini.
        if ($this->batas_tanggal) {
            $rules['tanggal_permintaan'] = 'required|date|before_or_equal:' . $this->batas_tanggal;
            $messages['tanggal_permintaan.before_or_equal'] = 'Tanggal tidak boleh melebihi batas selesai proyek (' . date('d M Y', strtotime($this->batas_tanggal)) . ').';
        } else {
            $rules['tanggal_permintaan'] = 'required|date';
        }

        $this->validate($rules, $messages);

        // Pengecekan Penugasan Proyek
        $isAssigned = DB::table('penugasan_proyek')
            ->where('id_user', Auth::id())
            ->where('id_proyek', $this->id_proyek)
            ->where('status_penugasan', 'Aktif')
            ->exists();

        if (!$isAssigned) {
            $this->addError('id_proyek', 'Anda tidak memiliki penugasan aktif di proyek ini.');
            return;
        }

        DB::transaction(function () {
            if ($this->isEditMode) {
                // ==========================================
                // PROSES UPDATE / REVISI DATA
                // ==========================================
                $header = PermintaanProyek::findOrFail($this->edit_id);
                
                $header->update([
                    'id_proyek' => $this->id_proyek,
                    'tanggal_permintaan' => $this->tanggal_permintaan,
                    
                    // KEMBALIKAN STATUS & HAPUS CATATAN (Logika Skema 2)
                    'status_permintaan' => 'Menunggu Persetujuan',
                    'catatan_penolakan' => null 
                ]);

                // Hapus detail lama, insert detail baru
                DetailPermintaanProyek::where('id_permintaan', $this->edit_id)->delete();

                foreach ($this->items as $item) {
                    DetailPermintaanProyek::create([
                        'id_permintaan' => $header->id_permintaan,
                        'id_material' => $item['id_material'],
                        'jumlah_diminta' => $item['jumlah_diminta'],
                    ]);
                }
                session()->flash('message', 'Revisi permintaan material berhasil disimpan dan diajukan ulang!');
            } else {
                // ==========================================
                // PROSES CREATE DATA BARU
                // ==========================================
                $header = PermintaanProyek::create([
                    'id_proyek' => $this->id_proyek,
                    'id_user' => Auth::id(),
                    'tanggal_permintaan' => $this->tanggal_permintaan,
                    'status_permintaan' => 'Menunggu Persetujuan',
                ]);

                foreach ($this->items as $item) {
                    DetailPermintaanProyek::create([
                        'id_permintaan' => $header->id_permintaan,
                        'id_material' => $item['id_material'],
                        'jumlah_diminta' => $item['jumlah_diminta'],
                    ]);
                }
                session()->flash('message', 'Permintaan material berhasil diajukan!');
            }
        });

        // Reset Pagination dan tutup modal setelah transaksi database sukses
        $this->resetPage(); 
        $this->closeModal();
    }

  public function render()
    {
        $usedPermintaanIds = PenggunaanMaterial::pluck('id_permintaan')->toArray();    

        $permintaans = PermintaanProyek::with(['proyek'])
            ->where('id_user', Auth::id())
            ->where(function($q) {
                $q->where('id_permintaan', 'like', "%{$this->search}%")
                  ->orWhereHas('proyek', function($sq) { 
                      $sq->where('nama_proyek', 'like', "%{$this->search}%"); 
                  });
            })
            ->when($this->filterStatus, function($q) {
                $q->where('status_permintaan', $this->filterStatus);
            })
            // ---> TAMBAHKAN FILTER PROYEK DI SINI <---
            ->when($this->filterProyek, function($q) {
                $q->where('id_proyek', $this->filterProyek);
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate(10);

        // Data listProyek sudah ada di kode Anda sebelumnya, kita akan gunakan ini untuk dropdown
        $assignedProyekIds = DB::table('penugasan_proyek')
            ->where('id_user', Auth::id())
            ->where('status_penugasan', 'Aktif')
            ->pluck('id_proyek');

        $listProyek = Proyek::where('status_proyek', 'Aktif')
            ->whereIn('id_proyek', $assignedProyekIds)
            ->get();

        return view('livewire.pelaksanaan.permintaan-proyek.index-permintaan', [
            'permintaans' => $permintaans,
            'listProyek' => $listProyek,
            'listMaterial' => Material::all(),
            'usedPermintaanIds' => $usedPermintaanIds
        ]);
    }
}