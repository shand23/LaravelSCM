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
       
     $this->min_tanggal_berangkat = null; // Step 0: Reset tanggal setiap kali ganti PO

    if (!$id_kontrak) return;
    
        $kontrak = Kontrak::with('detailKontrak.material')->find($id_kontrak);
        if ($kontrak) {
             $this->min_tanggal_berangkat = $kontrak->tanggal_kontrak;
            foreach ($kontrak->detailKontrak as $detail) {
                $sudahDikirim = PengirimanDetail::whereHas('pengiriman', function($q) use ($id_kontrak) {
                                    $q->where('id_kontrak', $id_kontrak);
                                })
                                ->where('id_detail_kontrak', $detail->id_detail_kontrak)
                                ->sum('jumlah_dikirim');
                
                $jumlahRusak = DB::table('detail_penerimaan')
                    ->join('penerimaan_material', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan_material.id_penerimaan')
                    ->join('pengiriman', 'penerimaan_material.id_pengiriman', '=', 'pengiriman.id_pengiriman')
                    ->where('pengiriman.id_kontrak', $id_kontrak)
                    ->where('detail_penerimaan.id_detail_kontrak', $detail->id_detail_kontrak)
                    ->sum('detail_penerimaan.jumlah_rusak');

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

            // Kembalikan ke struktur 'details' agar tidak error
            $detailsOtomatis = [];
            foreach ($this->listMaterialPO as $mat) {
                $detailsOtomatis[] = [
                    'id_detail_kontrak' => $mat['id_detail_kontrak'],
                    'qty' => $mat['sisa_kebutuhan']
                ];
            }

            if (empty($detailsOtomatis)) {
                $detailsOtomatis = [['id_detail_kontrak' => '', 'qty' => 0]];
            }

            $this->jadwals = [[
                'tanggal_berangkat' => '',
                'estimasi_tanggal_tiba' => '',
                'nama_supir' => '',
                'plat_kendaraan' => '',
                'keterangan' => '',
                'details' => $detailsOtomatis
            ]];
        }
    }

   public function addJadwal()
    {
        $newDetails = [];
        
        foreach ($this->listMaterialPO as $mat) {
            $dialokasikan = 0;
            foreach ($this->jadwals as $jadwal) {
                if (isset($jadwal['details'])) {
                    foreach ($jadwal['details'] as $det) {
                        if (($det['id_detail_kontrak'] ?? '') == $mat['id_detail_kontrak']) {
                            $dialokasikan += (int)($det['qty'] ?? 0);
                        }
                    }
                }
            }
            $sisa = max(0, $mat['sisa_kebutuhan'] - $dialokasikan);
            
            if ($sisa > 0) {
                $newDetails[] = [
                    'id_detail_kontrak' => $mat['id_detail_kontrak'],
                    'qty' => $sisa
                ];
            }
        }

        if (empty($newDetails)) {
            $newDetails = [['id_detail_kontrak' => '', 'qty' => 0]];
        }

        $this->jadwals[] = [
            'tanggal_berangkat' => '',
            'estimasi_tanggal_tiba' => '',
            'nama_supir' => '',
            'plat_kendaraan' => '',
            'keterangan' => '',
            'details' => $newDetails
        ];
    }

    // --- FUNGSI BARU: AUTO-KOREKSI INPUT JUMLAH AGAR TIDAK LEBIH DARI PO ---
   public function updated($propertyName)
    {
        if (preg_match('/^jadwals\.(\d+)\.details\.(\d+)\.qty$/', $propertyName, $matches)) {
            $index = (int) $matches[1];
            $detIndex = (int) $matches[2];

            $id_dk = $this->jadwals[$index]['details'][$detIndex]['id_detail_kontrak'] ?? null;
            if ($id_dk) {
                $matInfo = collect($this->listMaterialPO)->firstWhere('id_detail_kontrak', $id_dk);
                if ($matInfo) {
                    $totalPO = $matInfo['sisa_kebutuhan'];
                    
                    // 1. Hitung kuota yang sudah terpakai di truk-truk SEBELUMNYA
                    $alokasiSebelumnya = 0;
                    for ($i = 0; $i < $index; $i++) {
                        if (isset($this->jadwals[$i]['details'])) {
                            foreach ($this->jadwals[$i]['details'] as $det) {
                                if (($det['id_detail_kontrak'] ?? '') == $id_dk) {
                                    $alokasiSebelumnya += (int)($det['qty'] ?? 0);
                                }
                            }
                        }
                    }

                    // 2. Koreksi input di truk SAAT INI (Tidak boleh lebih dari sisa yang ada)
                    $sisaTersedia = max(0, $totalPO - $alokasiSebelumnya);
                    $nilaiDiketik = (int)($this->jadwals[$index]['details'][$detIndex]['qty'] ?? 0);

                    if ($nilaiDiketik > $sisaTersedia) {
                        $this->jadwals[$index]['details'][$detIndex]['qty'] = $sisaTersedia;
                        $nilaiDiketik = $sisaTersedia;
                    } elseif ($nilaiDiketik < 0) {
                        $this->jadwals[$index]['details'][$detIndex]['qty'] = 0;
                        $nilaiDiketik = 0;
                    }

                    // 3. Distribusikan SISA KUOTA ke truk BERIKUTNYA secara otomatis
                    $sisaUntukBerikutnya = max(0, $sisaTersedia - $nilaiDiketik);
                    for ($i = $index + 1; $i < count($this->jadwals); $i++) {
                        if (isset($this->jadwals[$i]['details'])) {
                            foreach ($this->jadwals[$i]['details'] as $k => $det) {
                                if (($det['id_detail_kontrak'] ?? '') == $id_dk) {
                                    $this->jadwals[$i]['details'][$k]['qty'] = $sisaUntukBerikutnya;
                                    // Sisa di-nol-kan karena sudah ditampung semua di truk berikutnya
                                    $sisaUntukBerikutnya = 0; 
                                }
                            }
                        }
                    }
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

    // --- FUNGSI BARU: UPDATE STATUS TIBA DI LOKASI ---
    public function markAsArrived($id_pengiriman)
    {
        try {
            $pengiriman = Pengiriman::find($id_pengiriman);
            
            if ($pengiriman) {
                $pengiriman->status_pengiriman = 'Tiba di Lokasi';
                $pengiriman->save();

                session()->flash('message', 'Status pengiriman berhasil diupdate menjadi Tiba di Lokasi.');
            } else {
                session()->flash('error', 'Data pengiriman tidak ditemukan.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

public function cetakDO($id_pengiriman)
    {
        try {
            // Tambahkan 'kontrak.supplier' agar data supplier ikut terambil
            $pengiriman = Pengiriman::with([
                'kontrak.supplier', 
                'detailPengiriman.detailKontrak.material'
            ])->where('id_pengiriman', $id_pengiriman)->firstOrFail();

            $pdf = Pdf::loadView('livewire.pengadaan.pengiriman.cetak-do-pdf', [
                'pengiriman' => $pengiriman
            ]);

            $pdf->setPaper('A4', 'portrait');

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'Surat_Jalan_' . $pengiriman->id_pengiriman . '.pdf');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mencetak dokumen: ' . $e->getMessage());
        }
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
        
        $do = Pengiriman::with('detailPengiriman.detailKontrak.material')->find($id);
        
        if (!$do || $do->status_pengiriman != 'Pending') {
            session()->flash('error', 'Data tidak ditemukan atau sudah diproses.');
            return;
        }

        $this->id_kontrak = $do->id_kontrak;
        $this->tipe_pengiriman = 'Bertahap';
        
        // Load data material yang masih ada sisa
        $this->loadDataPO($do->id_kontrak);

        $details = [];
        foreach ($do->detailPengiriman as $det) {
            
            // --- PERBAIKAN ERROR UNDEFINED KEY ---
            // Cek apakah barang ini sudah dimuat oleh loadDataPO atau belum
            $sudahAda = collect($this->listMaterialPO)->contains('id_detail_kontrak', $det->id_detail_kontrak);

            // Jika belum ada (karena sisa PO sudah 0), kita inject secara manual!
            if (!$sudahAda) {
                $materi = $det->detailKontrak;
                if ($materi && $materi->material) {
                    // Strukturnya sekarang SAMA PERSIS dengan fungsi loadDataPO Anda
                    $this->listMaterialPO[] = [
                        'id_material' => $materi->id_material,
                        'id_detail_kontrak' => $det->id_detail_kontrak, // <--- Ini yang bikin error sebelumnya
                        'nama_material' => $materi->material->nama_material,
                        'satuan' => $materi->material->satuan ?? 'Unit',
                        'sisa_kebutuhan' => $det->jumlah_dikirim // <--- Di sistem Anda pakai sisa_kebutuhan
                    ];
                }
            }
            // -------------------------------------

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

    // Fungsi khusus untuk menyimpan data yang diedit tanpa mengganggu store() asli
    public function update()
    {
        $this->validate([
            'jadwals.0.tanggal_berangkat' => 'required|date',
            'jadwals.0.estimasi_tanggal_tiba' => 'required|date',
        ]);

        try {
            $jadwal = $this->jadwals[0];
            
            // Validasi tanggal
            if (strtotime($jadwal['estimasi_tanggal_tiba']) < strtotime($jadwal['tanggal_berangkat'])) {
                session()->flash('error', "Estimasi tiba tidak boleh sebelum tanggal berangkat!");
                return;
            }

            $pengiriman = Pengiriman::find($this->edit_id);
            
            if ($pengiriman) {
                $pengiriman->update([
                    'tanggal_berangkat' => $jadwal['tanggal_berangkat'],
                    'estimasi_tanggal_tiba' => $jadwal['estimasi_tanggal_tiba'],
                    'keterangan' => $jadwal['keterangan'] ?? null,
                ]);

                session()->flash('message', 'Jadwal pengiriman berhasil diperbarui.');
                $this->closeModal();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
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
    // PERBAIKAN: Gunakan collection untuk mencari data array berdasarkan id_detail_kontrak
    $matInfo = collect($this->listMaterialPO)->firstWhere('id_detail_kontrak', $idDet);
    $sisa = $matInfo['sisa_kebutuhan'] ?? 0;

    if ($total > $sisa) {
        $namaMat = $matInfo['nama_material'] ?? 'Unknown';
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