<div wire:poll.3s class="p-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengiriman (Delivery Order)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola Proses Pengiriman Barang Berdasarkan Purchase Order</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Buat DO Baru
        </button>
    </div>

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="bg-white p-4 mb-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID Pengiriman..." 
                   class="pl-10 w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID DO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor PO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal & Estimasi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rincian Barang</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($listPengiriman as $p)
                <tr class="hover:bg-gray-50 transition-colors">
<td class="px-6 py-4 whitespace-nowrap">
    {{-- Menampilkan ID Pengiriman/DO --}}
    <div class="text-sm font-bold text-indigo-700">
        {{ $p->id_pengiriman }}
    </div>

    {{-- Keterangan Pembuat di bawahnya --}}
    <div class="flex items-center gap-1 mt-1">
        
        <span class="text-[10px] text-gray-500 italic">
            Procurement: <span class="font-medium text-gray-700">{{ $p->userPengadaan->nama_lengkap ?? 'Tidak Diketahui' }}</span>
        </span>
    </div>
</td>                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->kontrak->nomor_kontrak ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <div class="flex flex-col">
                            <span class="font-medium"><span class="text-gray-400">Berangkat:</span> {{ \Carbon\Carbon::parse($p->tanggal_berangkat)->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500 mt-1"><span class="text-gray-400">Tiba:</span> {{ \Carbon\Carbon::parse($p->estimasi_tanggal_tiba)->format('d M Y') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <ul class="text-xs space-y-1 bg-gray-50 p-2.5 rounded-md border border-gray-100 text-gray-700">
                            @foreach($p->detailPengiriman as $det)
                                <li class="flex justify-between gap-4">
                                    <span>• {{ $det->detailKontrak->material->nama_material ?? 'Unknown' }}</span>
                                    <span class="font-bold">x{{ $det->jumlah_dikirim }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @php
                            $statusClass = match($p->status_pengiriman) {
                                'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'Dalam Perjalanan' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Tiba di Lokasi' => 'bg-green-100 text-green-800 border-green-200',
                                'Selesai' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'Return & Kirim Ulang' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                            @if($p->status_pengiriman == 'Dalam Perjalanan') 🚛 @endif
                            {{ $p->status_pengiriman }}
                        </span>
                    </td>

                    

  <td class="px-6 py-4 text-center text-sm font-medium">
    <div class="flex justify-center items-center gap-2 flex-wrap">
        
        {{-- TOMBOL: KIRIM DO (Jika masih Pending) --}}
        @if($p->status_pengiriman == 'Pending')
            <button wire:click="kirimDO('{{ $p->id_pengiriman }}')" 
                    wire:confirm="Yakin ingin memproses pengiriman ini sekarang?" 
                    class="text-indigo-700 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-2.5 py-1.5 rounded-md border border-indigo-200 transition flex items-center gap-1" title="Kirim Sekarang">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                Kirim
            </button>
           @if($p->id_user_pengadaan == auth()->user()->id_user)
    {{-- Tombol Edit - Hanya muncul jika pembuatnya --}}
    <button wire:click="editDO('{{ $p->id_pengiriman }}')" class="text-amber-600 hover:text-amber-900 bg-amber-50 px-2.5 py-1.5 rounded-md border border-amber-200 transition" title="Edit">
        Edit
    </button>

    {{-- Tombol Hapus - Hanya muncul jika pembuatnya --}}
    <button wire:click="deleteDO('{{ $p->id_pengiriman }}')" 
            wire:confirm="Yakin ingin menghapus DO ini? Jadwal truk dan alokasi barang akan dibatalkan." 
            class="text-red-600 hover:text-red-900 bg-red-50 px-2.5 py-1.5 rounded-md border border-red-200 transition" title="Hapus">
        Hapus
    </button>
@else
    {{-- Tampilan jika staf lain yang melihat --}}
    <span class="text-gray-400 italic text-xs px-2">Read-Only</span>
@endif
        @endif

        

        {{-- TOMBOL BARU: TIBA DI LOKASI (Jika sedang Dalam Perjalanan) --}}
        @if($p->status_pengiriman == 'Dalam Perjalanan')
            <button wire:click="markAsArrived('{{ $p->id_pengiriman }}')" 
                    wire:confirm="Konfirmasi bahwa pengiriman ini telah tiba di lokasi/gudang?"
                    class="text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white px-2.5 py-1.5 rounded-md border border-emerald-200 shadow-sm transition flex items-center gap-1 font-bold" title="Tandai Tiba">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Tiba di Lokasi
            </button>
        @endif

        {{-- TOMBOL: CETAK DO (Cetak PDF) --}}
        <button wire:click="cetakDO('{{ $p->id_pengiriman }}')" class="text-gray-700 bg-gray-50 hover:bg-gray-200 px-2.5 py-1.5 rounded-md border border-gray-300 transition flex items-center gap-1" title="Cetak Surat Jalan">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            PDF
        </button>
 @if($p->status_pengiriman == 'Return & Kirim Ulang')
                            <button wire:click="prosesRetur('{{ $p->id_pengiriman }}')" class="text-orange-600 hover:text-orange-900 bg-orange-50 px-2.5 py-1.5 rounded-md border border-orange-200 transition flex items-center gap-1 shadow-sm font-bold" title="Kirim Ulang Barang Retur">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                Kirim Ulang
                            </button>

           @endif                 
        {{-- INFO TERKUNCI (Jika Selesai / Tiba di Lokasi) --}}
        @if($p->status_pengiriman == 'Tiba di Lokasi' || $p->status_pengiriman == 'Selesai')
            <span class="text-gray-400 bg-gray-50 px-2.5 py-1.5 rounded-md border border-gray-100 flex items-center gap-1 cursor-not-allowed text-xs" title="Data sudah masuk proses logistik">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Terkunci
            </span>
        @endif

        {{-- TOMBOL BUKTI RETUR (Jika DO ini memiliki retur) --}}
        @if(in_array($p->id_pengiriman, $doDenganRetur ?? []))
            <button wire:click="lihatDetailRetur('{{ $p->id_pengiriman }}')" class="text-purple-600 hover:text-purple-900 bg-purple-50 px-2.5 py-1.5 rounded-md border border-purple-200 transition flex items-center gap-1 shadow-sm font-bold" title="Lihat Bukti Barang Rusak">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Retur
            </button>
        @endif
        
    </div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data pengiriman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $listPengiriman->links() }}
        </div>
    </div>

    {{-- MODAL BUAT/EDIT PENGIRIMAN --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
            
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    @if($edit_id) Edit Pengiriman ({{ $edit_id }})
                    @elseif($id_do_retur) Kirim Ulang Barang Retur
                    @else Penerbitan Delivery Order (DO)
                    @endif
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto bg-white flex-1">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8 bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Nomor PO</label>
                        <select wire:model.live="id_kontrak" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 {{ ($edit_id || $id_do_retur) ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}" {{ ($edit_id || $id_do_retur) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kontrak --</option>
                            @foreach($listKontrak as $k)
                                <option value="{{ $k->id_kontrak }}">{{ $k->nomor_kontrak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Metode Kirim</label>
                        <select wire:model.live="tipe_pengiriman" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 {{ ($edit_id || $id_do_retur) ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}" {{ ($edit_id || $id_do_retur) ? 'disabled' : '' }}>
                            <option value="Sekaligus">Sekaligus (Semua Sisa PO)</option>
                            <option value="Bertahap">Bertahap (Pecah Truk / Pilih Material)</option>
                            <option value="Retur">Barang Retur / Pengganti</option>
                        </select>
                    </div>
                </div>

                @if($id_kontrak && count($listMaterialPO) > 0)
                <div class="mb-2 flex justify-between items-end border-b border-gray-200 pb-2">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Penjadwalan & Armada</h3>
                    
                    @if($tipe_pengiriman == 'Bertahap' && !$edit_id && !$id_do_retur)
                        <button wire:click="addJadwal" type="button" class="bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-md text-xs font-bold transition flex items-center gap-1 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Jadwal / Truk
                        </button>
                    @endif
                </div>

                <div class="space-y-6 mt-4 mb-4">
                    @foreach($jadwals as $index => $jadwal)
                    <div wire:key="truk-{{ $index }}" class="border border-gray-200 rounded-xl overflow-hidden shadow-sm bg-white">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <span class="font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Tahap Pengiriman / Truk Ke-{{ $index + 1 }}
                            </span>
                            @if(count($jadwals) > 1 && !$edit_id && !$id_do_retur)
                                <button wire:click="removeJadwal({{ $index }})" type="button" class="text-red-500 text-xs font-bold bg-white px-3 py-1 rounded-md border border-red-200 hover:bg-red-50 transition-colors">
                                    Hapus Truk
                                </button>
                            @endif
                        </div>
                        
                        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Tgl Berangkat</label>
                                {{-- PASTIKAN NAMA VARIABELNYA tanggal_berangkat --}}
                                <input type="date" wire:model.live="jadwals.{{ $index }}.tanggal_berangkat" min="{{ $min_tanggal_berangkat }}" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Estimasi Tiba</label>
                                <input type="date" wire:model.live="jadwals.{{ $index }}.estimasi_tanggal_tiba" min="{{ $jadwal['tanggal_berangkat'] ?? $min_tanggal_berangkat }}" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Keterangan Tambahan</label>
                                <input type="text" wire:model.live="jadwals.{{ $index }}.keterangan" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500" placeholder="Opsional...">
                            </div>
                        </div>

                        {{-- Rincian Barang per Truk --}}
                        <div class="bg-blue-50/30 p-5 border-t border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-xs font-bold text-blue-800 uppercase tracking-wider">Muatan Material</p>
                                @if(!$id_do_retur && $tipe_pengiriman != 'Sekaligus')
                                <button wire:click="addMaterialToJadwal({{ $index }})" type="button" {{ $edit_id ? 'disabled' : '' }} class="text-xs bg-white text-blue-600 border border-blue-200 font-bold px-3 py-1.5 rounded-md hover:bg-blue-50 shadow-sm transition">
                                    + Tambah Item
                                </button>
                                @endif
                            </div>
                            
                            <div class="space-y-3">
                                
                                @foreach($jadwal['details'] ?? [] as $detIndex => $detail)
                                    @php
                                        // Hitung total material yang sudah terisi di semua truk saat ini
                                        $alokasiTotal = 0;
                                        foreach($jadwals as $j) {
                                            if(isset($j['details'])) {
                                                foreach($j['details'] as $d) {
                                                    if(($d['id_detail_kontrak'] ?? '') == ($detail['id_detail_kontrak'] ?? '')) {
                                                        $alokasiTotal += (int)($d['qty'] ?? 0);
                                                    }
                                                }
                                            }
                                        }
                                        
                                        // Dapatkan batas maksimal dari PO
                                        $totalPOMaterial = 0;
                                        foreach($listMaterialPO as $mat) {
                                            if($mat['id_detail_kontrak'] == ($detail['id_detail_kontrak'] ?? '')) {
                                                $totalPOMaterial = $mat['sisa_kebutuhan'];
                                                break;
                                            }
                                        }
                                        
                                        // Sisa indikator realtime
                                        $sisaIndikator = max(0, $totalPOMaterial - $alokasiTotal);
                                    @endphp

                                <div wire:key="truk-{{ $index }}-det-{{ $detIndex }}" class="flex gap-4 items-end bg-white p-3.5 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex-1">
    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Material (Sisa PO)</label>
    <select wire:model.live="jadwals.{{ $index }}.details.{{ $detIndex }}.id_detail_kontrak" {{ $edit_id ? 'disabled' : '' }} class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 {{ ($id_do_retur || $tipe_pengiriman == 'Sekaligus') ? 'bg-gray-100 pointer-events-none' : '' }}" {{ ($id_do_retur || $tipe_pengiriman == 'Sekaligus') ? 'disabled' : '' }}>
        <option value="">-- Pilih Material --</option>
        @foreach($listMaterialPO as $item)
            @php
                // LOGIKA BARU: Mencegah material yang sama dipilih 2x di truk yang sama
                $sudahDipilih = false;
                foreach($jadwal['details'] ?? [] as $k => $d) {
                    if($k != $detIndex && ($d['id_detail_kontrak'] ?? '') == $item['id_detail_kontrak']) {
                        $sudahDipilih = true; 
                        break;
                    }
                }
            @endphp
            
            {{-- Tampilkan hanya jika belum dipilih di baris lain --}}
            @if(!$sudahDipilih)
                <option value="{{ $item['id_detail_kontrak'] }}">
                    {{ $item['nama_material'] }} (Kuota PO Tersedia: {{ $item['sisa_kebutuhan'] }})
                </option>
            @endif
        @endforeach
    </select>
</div>
                                    <div class="w-32">
                                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1 text-center">
                                            Jumlah 
                                            <span class="{{ $sisaIndikator == 0 ? 'text-green-600 font-bold' : 'text-orange-500' }}">
                                                (Maks: {{ $sisaIndikator }})
                                            </span>
                                        </label>
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="jadwals.{{ $index }}.details.{{ $detIndex }}.qty" 
                                               min="0" {{ $edit_id ? 'disabled' : '' }}
                                               oninput="this.value = Math.abs(this.value)" 
                                               class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm font-bold text-center focus:ring-blue-500 focus:border-blue-500 {{ $tipe_pengiriman == 'Sekaligus' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'text-blue-700' }}"
                                               {{ $tipe_pengiriman == 'Sekaligus' ? 'readonly' : '' }}>
                                    </div>
                                    
                                    @if(count($jadwal['details'] ?? []) > 1 && !$id_do_retur && $tipe_pengiriman != 'Sekaligus')
                                    <div>
                                        <button wire:click="removeMaterialFromJadwal({{ $index }}, {{ $detIndex }})" type="button" class="text-red-400 hover:text-red-600 p-2.5 bg-red-50 hover:bg-red-100 rounded-md transition" title="Hapus baris ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button wire:click="closeModal" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Batal
                </button>
               @if($edit_id)
        {{-- Jika sedang EDIT, panggil fungsi update() --}}
        <button type="button" wire:click="update" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg font-semibold">
            Simpan Perubahan Jadwal
        </button>
    @else
        {{-- Jika sedang BARU, panggil fungsi store() asli Anda --}}
        <button type="button" wire:click="store" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
            Simpan DO Baru
        </button>
    @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL BUKTI BARANG RUSAK (TETAP SAMA) --}}
    @if($isModalDetailReturOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-red-800 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Informasi Barang Rusak / Ditolak (DO: {{ $infoDORetur->id_pengiriman ?? '-' }})
                </h2>
                <button wire:click="closeDetailReturModal" class="text-red-400 hover:text-red-700 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto bg-white flex-1">
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Rusak</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($dataDetailRetur as $retur)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $retur->nama_material }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 font-bold text-center text-lg">{{ $retur->jumlah_rusak }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 italic">"{{ $retur->alasan_return ?? '-' }}"</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button wire:click="closeDetailReturModal" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium transition-colors">Tutup</button>
                <button wire:click="cetakBuktiRetur('{{ $infoDORetur->id_pengiriman ?? '' }}')" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center gap-2">Cetak PDF</button>
            </div>
        </div>
    </div>
    @endif
</div>