<?php

namespace App\Livewire\Pengadaan\Pengiriman;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Kontrak;
use App\Models\InvoicePembelian; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class PengirimanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    
    // --- State Baru untuk Fitur Detail Retur / Bukti Rusak ---
    public $isModalDetailReturOpen = false;
    public $dataDetailRetur = [];
    public $infoDORetur = null;
    // ---------------------------------------------------------

    public $edit_id = null; 
    public $id_do_retur = null; 
    public $id_kontrak;
    public $tipe_pengiriman = 'Sekaligus'; 
    public $listMaterialPO = []; 
    public $jadwals = []; 
    public $min_tanggal_berangkat;

    public function updatedIdKontrak($value)
    {
        $this->loadDataPO($value);
    }

    public function updatedTipePengiriman()
    {
        $this->loadDataPO($this->id_kontrak);
    }

   private function loadDataPO($id_kontrak)
    {
        $this->listMaterialPO = [];
        $this->jadwals = [];

        if (!$id_kontrak) return;

        $kontrak = Kontrak::with('detailKontrak.material')->find($id_kontrak);
        if ($kontrak) {
            foreach ($kontrak->detailKontrak as $detail) {
                // Hitung jumlah yang sudah masuk ke proses pengiriman
                $sudahDikirim = PengirimanDetail::whereHas('pengiriman', function($q) use ($id_kontrak) {
                                    $q->where('id_kontrak', $id_kontrak);
                                })
                                ->where('id_detail_kontrak', $detail->id_detail_kontrak)
                                ->sum('jumlah_dikirim');
                
                // Cek jika ada retur rusak yang perlu dikirim ulang
                $jumlahRusak = DB::table('detail_penerimaan')
                    ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
                    ->join('pengiriman', 'penerimaan_material.id_pengiriman', '=', 'pengiriman.id_pengiriman')
                    ->where('pengiriman.id_kontrak', $id_kontrak)
                    ->where('detail_penerimaan.id_detail_kontrak', $detail->id_detail_kontrak)
                    ->sum('detail_penerimaan.jumlah_rusak');

                // Sisa kebutuhan = Final PO - (Yang sudah dikirim - Yang Rusak)
                $sisa = $detail->jumlah_final - ($sudahDikirim - $jumlahRusak);

                if ($sisa > 0) {
                    $this->listMaterialPO[] = [
                        'id_material' => $detail->id_material,
                        'id_detail_kontrak' => $detail->id_detail_kontrak,
                        'nama_material' => $detail->material->nama_material,
                        'satuan' => $detail->material->satuan ?? 'Unit',
                        'sisa_kebutuhan' => $sisa
                    ];
                }
            }

            // Inisialisasi Jadwal Truk Ke-1 (Otomatis Full Kuota Sisa)
            $this->jadwals = [[
                'tanggal_berangkat' => '',
                'estimasi_tanggal_tiba' => '',
                'nama_supir' => '',
                'plat_kendaraan' => '',
                'items' => []
            ]];

            foreach ($this->listMaterialPO as $mat) {
                // Baik "Sekaligus" maupun "Bertahap", default awal selalu sisa kebutuhan penuh
                $this->jadwals[0]['items'][$mat['id_material']] = $mat['sisa_kebutuhan'];
            }
        }
    }

   public function addJadwal()
    {
        $newItem = [];
        
        // Logika Pintar: Otomatis menghitung sisa dari truk-truk sebelumnya
        foreach ($this->listMaterialPO as $mat) {
            $dialokasikan = 0;
            foreach ($this->jadwals as $jadwal) {
                $dialokasikan += (int)($jadwal['items'][$mat['id_material']] ?? 0);
            }
            // Sisa untuk truk baru adalah Total Kebutuhan - Yang sudah ditaruh di truk lain
            $sisa = max(0, $mat['sisa_kebutuhan'] - $dialokasikan);
            $newItem[$mat['id_material']] = $sisa;
        }

        $this->jadwals[] = [
            'tanggal_berangkat' => '',
            'estimasi_tanggal_tiba' => '',
            'nama_supir' => '',
            'plat_kendaraan' => '',
            'items' => $newItem
        ];
    }

    // --- FUNGSI BARU: AUTO-KOREKSI INPUT JUMLAH AGAR TIDAK LEBIH DARI PO ---
    public function updated($propertyName)
    {
        // Deteksi jika yang diubah adalah input jumlah material di dalam truk/jadwal tertentu
        if (preg_match('/^jadwals\.(\d+)\.items\.(.+)$/', $propertyName, $matches)) {
            $index = $matches[1];
            $id_material = $matches[2];

            $matInfo = collect($this->listMaterialPO)->firstWhere('id_material', $id_material);
            
            if ($matInfo) {
                $sisaKebutuhan = $matInfo['sisa_kebutuhan'];
                $alokasiTrukLain = 0;
                
                // Hitung total material yang sudah dialokasikan di truk LAIN (bukan truk yang sedang diketik)
                foreach ($this->jadwals as $idx => $jadwal) {
                    if ($idx != $index) {
                        $alokasiTrukLain += (int)($jadwal['items'][$id_material] ?? 0);
                    }
                }

                $maxTersedia = max(0, $sisaKebutuhan - $alokasiTrukLain);
                $nilaiDiketik = (int)$this->jadwals[$index]['items'][$id_material];

                // Jika mengetik lebih dari sisa yang ada, paksa kembali ke batas maksimal
                if ($nilaiDiketik > $maxTersedia) {
                    $this->jadwals[$index]['items'][$id_material] = $maxTersedia;
                } elseif ($nilaiDiketik < 0) {
                    $this->jadwals[$index]['items'][$id_material] = 0;
                }
            }
        }
    }

    public function removeJadwal($index)
    {
        unset($this->jadwals[$index]);
        $this->jadwals = array_values($this->jadwals); 
    }

    public function addMaterialToJadwal($jadwalIndex)
    {
        $this->jadwals[$jadwalIndex]['details'][] = ['id_detail_kontrak' => '', 'qty' => 0];
    }

    public function removeMaterialFromJadwal($jadwalIndex, $detailIndex)
    {
        unset($this->jadwals[$jadwalIndex]['details'][$detailIndex]);
        $this->jadwals[$jadwalIndex]['details'] = array_values($this->jadwals[$jadwalIndex]['details']);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function lihatDetailRetur($id_pengiriman)
    {
        $this->infoDORetur = Pengiriman::find($id_pengiriman);

        $this->dataDetailRetur = DB::table('detail_penerimaan')
            ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
            ->join('detail_kontrak', 'detail_penerimaan.id_detail_kontrak', '=', 'detail_kontrak.id_detail_kontrak')
            ->join('material', 'detail_kontrak.id_material', '=', 'material.id_material') 
            ->where('penerimaan_material.id_pengiriman', $id_pengiriman)
            ->where('detail_penerimaan.jumlah_rusak', '>', 0)
            ->select(
                'material.nama_material',
                'detail_penerimaan.jumlah_rusak',
                'detail_penerimaan.alasan_return', 
                'detail_penerimaan.foto_bukti_rusak' 
            )
            ->get();

        $this->isModalDetailReturOpen = true;
    }

    public function cetakBuktiRetur($id_pengiriman)
    {
        $doRetur = Pengiriman::with('kontrak')->findOrFail($id_pengiriman);

        $dataDetailRetur = DB::table('detail_penerimaan')
            ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
            ->join('detail_kontrak', 'detail_penerimaan.id_detail_kontrak', '=', 'detail_kontrak.id_detail_kontrak')
            ->join('material', 'detail_kontrak.id_material', '=', 'material.id_material') 
            ->where('penerimaan_material.id_pengiriman', $id_pengiriman)
            ->where('detail_penerimaan.jumlah_rusak', '>', 0)
            ->select(
                'material.nama_material',
                'detail_penerimaan.jumlah_rusak',
                'detail_penerimaan.alasan_return',
                'detail_penerimaan.foto_bukti_rusak' 
            )
            ->get();

        $nomorDO = $doRetur->nomor_pengiriman ?? 'DO-' . $id_pengiriman;
        $namaFile = 'Bukti-Retur-' . str_replace('/', '-', $nomorDO) . '.pdf';

        $pdf = Pdf::loadView('livewire.pengadaan.pengiriman.pdf-retur', [
            'doRetur' => $doRetur,
            'dataDetailRetur' => $dataDetailRetur
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $namaFile);
    }

    public function closeDetailReturModal()
    {
        $this->isModalDetailReturOpen = false;
        $this->dataDetailRetur = [];
        $this->infoDORetur = null;
    }

    public function prosesRetur($id_pengiriman)
    {
        $this->resetForm();
        $doRetur = Pengiriman::find($id_pengiriman);
        
        if (!$doRetur) return;

        $this->id_do_retur = $id_pengiriman; 
        $this->id_kontrak = $doRetur->id_kontrak;
        $this->tipe_pengiriman = 'Retur';
        
        $this->loadDataPO($this->id_kontrak);

        $barangRusak = DB::table('detail_penerimaan')
            ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
            ->where('penerimaan_material.id_pengiriman', $id_pengiriman)
            ->where('detail_penerimaan.jumlah_rusak', '>', 0)
            ->select('detail_penerimaan.id_detail_kontrak', 'detail_penerimaan.jumlah_rusak')
            ->get();

        $details = [];
        foreach ($barangRusak as $br) {
            $details[] = [
                'id_detail_kontrak' => $br->id_detail_kontrak,
                'qty' => $br->jumlah_rusak
            ];
        }

        $this->jadwals = [[
            'tanggal_berangkat' => date('Y-m-d'),
            'estimasi_tanggal_tiba' => date('Y-m-d', strtotime('+1 day')),
            'keterangan' => 'Penggantian Retur dari DO: ' . $id_pengiriman,
            'details' => count($details) > 0 ? $details : [['id_detail_kontrak' => '', 'qty' => 0]]
        ]];

        $this->isModalOpen = true;
    }

    public function editDO($id)
    {
        $this->resetForm();
        $this->edit_id = $id;
        
        $do = Pengiriman::with('detailPengiriman')->find($id);
        if (!$do || $do->status_pengiriman != 'Pending') return;

        $this->id_kontrak = $do->id_kontrak;
        $this->tipe_pengiriman = 'Bertahap';
        
        $this->loadDataPO($do->id_kontrak);

        $details = [];
        foreach ($do->detailPengiriman as $det) {
            $details[] = [
                'id_detail_kontrak' => $det->id_detail_kontrak,
                'qty' => $det->jumlah_dikirim
            ];
        }

        $this->jadwals = [[
            'tanggal_berangkat' => \Carbon\Carbon::parse($do->tanggal_berangkat)->format('Y-m-d'),
            'estimasi_tanggal_tiba' => \Carbon\Carbon::parse($do->estimasi_tanggal_tiba)->format('Y-m-d'),
            'keterangan' => $do->keterangan,
            'details' => $details
        ]];

        $this->isModalOpen = true;
    }

    public function kirimDO($id)
    {
        $do = Pengiriman::find($id);
        if ($do && $do->status_pengiriman == 'Pending') {
            $do->update(['status_pengiriman' => 'Dalam Perjalanan']);
            session()->flash('message', "DO {$id} berhasil dikirim (Status: Dalam Perjalanan).");
        }
    }

    public function deleteDO($id)
    {
        $do = Pengiriman::find($id);
        if ($do && $do->status_pengiriman == 'Pending') {
            $do->delete();
            session()->flash('message', "DO {$id} berhasil dihapus.");
        }
    }

    public function store()
    {
        $this->validate([
            'id_kontrak' => 'required',
            'jadwals.*.tanggal_berangkat' => 'required|date',
            'jadwals.*.estimasi_tanggal_tiba' => 'required|date',
            'jadwals.*.details.*.qty' => 'required|numeric|min:0', 
        ], [
            'jadwals.*.details.*.qty.min' => 'Jumlah material tidak boleh bernilai minus (-).'
        ]);

        foreach ($this->jadwals as $index => $jadwal) {
            if (strtotime($jadwal['estimasi_tanggal_tiba']) < strtotime($jadwal['tanggal_berangkat'])) {
                session()->flash('error', "Estimasi tiba tidak boleh sebelum tanggal berangkat!");
                return;
            }
        }

        $totalKirimPerMaterial = [];
        foreach ($this->jadwals as $j) {
            foreach ($j['details'] as $det) {
                if (!empty($det['id_detail_kontrak'])) {
                    $id = $det['id_detail_kontrak'];
                    $totalKirimPerMaterial[$id] = ($totalKirimPerMaterial[$id] ?? 0) + (float) $det['qty'];
                }
            }
        }

        foreach ($totalKirimPerMaterial as $idDet => $total) {
            $sisa = $this->listMaterialPO[$idDet]['sisa_kebutuhan'] ?? 0;
            if ($total > $sisa) {
                $namaMat = $this->listMaterialPO[$idDet]['nama_material'] ?? 'Unknown';
                session()->flash('error', "Total kirim {$namaMat} melebihi sisa PO! (Maksimal: {$sisa})");
                return;
            }
        }

        DB::transaction(function () {
            if ($this->edit_id) {
                $jadwal = $this->jadwals[0];
                $do = Pengiriman::find($this->edit_id);
                
                $do->update([
                    'tanggal_berangkat' => $jadwal['tanggal_berangkat'],
                    'estimasi_tanggal_tiba' => $jadwal['estimasi_tanggal_tiba'],
                    'keterangan' => $jadwal['keterangan'],
                ]);

                PengirimanDetail::where('id_pengiriman', $this->edit_id)->delete();
                
                foreach ($jadwal['details'] as $det) {
                    if (!empty($det['id_detail_kontrak']) && $det['qty'] > 0) {
                        PengirimanDetail::create([
                            'id_pengiriman' => $do->id_pengiriman,
                            'id_detail_kontrak' => $det['id_detail_kontrak'], 
                            'jumlah_dikirim' => $det['qty'],
                        ]);
                    }
                }
            } else {
                foreach ($this->jadwals as $jadwal) {
                    $hasItems = false;
                    foreach ($jadwal['details'] as $det) {
                        if (!empty($det['id_detail_kontrak']) && $det['qty'] > 0) $hasItems = true;
                    }

                    if ($hasItems) {
                        $pengiriman = Pengiriman::create([
                            'id_kontrak' => $this->id_kontrak,
                            'id_user_pengadaan' => Auth::id() ?? 1,
                            'tanggal_berangkat' => $jadwal['tanggal_berangkat'],
                            'estimasi_tanggal_tiba' => $jadwal['estimasi_tanggal_tiba'],
                            'keterangan' => $jadwal['keterangan'],
                            'status_pengiriman' => 'Pending',
                        ]);

                        foreach ($jadwal['details'] as $det) {
                            if (!empty($det['id_detail_kontrak']) && $det['qty'] > 0) {
                                PengirimanDetail::create([
                                    'id_pengiriman' => $pengiriman->id_pengiriman,
                                    'id_detail_kontrak' => $det['id_detail_kontrak'], 
                                    'jumlah_dikirim' => $det['qty'],
                                ]);
                            }
                        }
                    }
                }

                if ($this->id_do_retur) {
                    $doLama = Pengiriman::find($this->id_do_retur);
                    if ($doLama) {
                        $doLama->update(['status_pengiriman' => 'Selesai']);
                    }
                }
            }
        });

        session()->flash('message', $this->edit_id ? 'DO Berhasil Diperbarui!' : 'DO Berhasil Disimpan!');
        $this->closeModal();
    }

    public function closeModal() { $this->isModalOpen = false; }

    private function resetForm() {
        $this->reset(['id_kontrak', 'tipe_pengiriman', 'listMaterialPO', 'jadwals', 'edit_id', 'id_do_retur']);
        $this->tipe_pengiriman = 'Sekaligus';
    }

    public function render()
    {
        // 1. Ambil ID Kontrak yang status invoice-nya sudah memenuhi syarat
        $validKontrakIds = InvoicePembelian::whereIn('status_invoice', ['Dibayar Sebagian', 'Lunas'])
            ->pluck('id_kontrak')
            ->toArray();

        // 2. Tambahkan whereIn id_kontrak pada query rawKontrak
        $rawKontrak = Kontrak::whereIn('status_kontrak', ['Disepakati', 'Pengiriman', 'Dikirim Sebagian'])
            ->whereIn('id_kontrak', $validKontrakIds)
            ->with('detailKontrak')
            ->get();
        
        $listKontrakValid = [];
        
        foreach ($rawKontrak as $kontrak) {
            if ($this->id_kontrak == $kontrak->id_kontrak) {
                $listKontrakValid[] = $kontrak;
                continue;
            }

            $adaSisa = false;
            foreach ($kontrak->detailKontrak as $detail) {
                $sudahDikirim = PengirimanDetail::whereHas('pengiriman', function($q) use ($kontrak) {
                    $q->where('id_kontrak', $kontrak->id_kontrak);
                })->where('id_detail_kontrak', $detail->id_detail_kontrak)->sum('jumlah_dikirim');

                $jumlahRusak = DB::table('detail_penerimaan')
                    ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
                    ->join('pengiriman', 'penerimaan_material.id_pengiriman', '=', 'pengiriman.id_pengiriman')
                    ->where('pengiriman.id_kontrak', $kontrak->id_kontrak)
                    ->where('detail_penerimaan.id_detail_kontrak', $detail->id_detail_kontrak)
                    ->sum('detail_penerimaan.jumlah_rusak');

                $sisaKebutuhan = $detail->jumlah_final - ($sudahDikirim - $jumlahRusak);
                
                if ($sisaKebutuhan > 0) {
                    $adaSisa = true;
                    break; 
                }
            }

            if ($adaSisa) {
                $listKontrakValid[] = $kontrak;
            }
        }

        $doDenganRetur = DB::table('detail_penerimaan')
            ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
            ->where('detail_penerimaan.jumlah_rusak', '>', 0)
            ->pluck('penerimaan_material.id_pengiriman')
            ->toArray();

        return view('livewire.pengadaan.pengiriman.pengiriman-index', [
            'listPengiriman' => Pengiriman::with(['kontrak', 'detailPengiriman.detailKontrak.material'])
                ->orderBy('created_at', 'desc')
                ->where('id_pengiriman', 'like', "%{$this->search}%")
                ->paginate(10),
            'listKontrak' => collect($listKontrakValid),
            'doDenganRetur' => $doDenganRetur 
        ]);
    }
}