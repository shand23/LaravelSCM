{{-- SEMUA HARUS DI DALAM SATU DIV ROOT INI --}}
<div class="p-6 bg-gray-50 min-h-screen">
    
    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800">Logistik: Penerimaan Material</h1>
            <p class="text-sm text-gray-500 uppercase tracking-widest font-semibold">Gudang & Inventory System</p>
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transition-all transform hover:scale-105 font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Proses Material Masuk
        </button>
    </div>

    {{-- Alert --}}
    @if (session()->has('message'))
        <div class="bg-emerald-500 text-white p-4 rounded-xl mb-6 shadow-md flex items-center gap-3 animate-bounce">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b flex items-center gap-4 bg-white">
            <div class="relative w-full md:w-1/3">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari ID Penerimaan..." class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">ID Penerimaan</th>
                    <th class="px-6 py-4">Nomor DO</th>
                    <th class="px-6 py-4 text-center">Tgl Terima</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($listPenerimaan as $p)
                <tr class="hover:bg-indigo-50/30 transition">
                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $p->id_penerimaan }}</td>
                    <td class="px-6 py-4 font-medium text-gray-700">{{ $p->id_pengiriman }}</td>
                    <td class="px-6 py-4 text-center">{{ \Carbon\Carbon::parse($p->tanggal_terima)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold {{ $p->status_penerimaan == 'Diterima Penuh' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ strtoupper($p->status_penerimaan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="text-indigo-600 hover:text-indigo-900 font-bold">Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data penerimaan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-gray-50">{{ $listPenerimaan->links() }}</div>
    </div>

    {{-- MODAL INPUT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="resetForm"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col animate-modal-up">
            
            <div class="px-8 py-5 border-b bg-gray-50 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black text-gray-800">Inspeksi & QC Material</h2>
                    <p class="text-xs text-gray-500 font-medium">Pastikan jumlah fisik sesuai dengan surat jalan (DO)</p>
                </div>
                <button wire:click="resetForm" class="p-2 hover:bg-red-100 text-gray-400 hover:text-red-500 rounded-full transition">✕</button>
            </div>

            <div class="p-8 overflow-y-auto flex-1">
                {{-- Form Header --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Pilih Nomor DO Tiba</label>
                        <select wire:model.live="id_pengiriman" class="w-full border-gray-200 rounded-xl py-3 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Surat Jalan --</option>
                            @foreach($listPengirimanDO as $do)
                                <option value="{{ $do->id_pengiriman }}">{{ $do->id_pengiriman }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Tanggal Terima</label>
                        <input type="date" wire:model="tanggal_terima" class="w-full border-gray-200 rounded-xl py-3 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Status Penilaian QC</label>
                        <div @class([
                            'w-full py-3 px-4 rounded-xl text-sm font-black border',
                            'bg-emerald-50 text-emerald-700 border-emerald-200' => $status_penerimaan == 'Diterima Penuh',
                            'bg-amber-50 text-amber-700 border-amber-200' => $status_penerimaan != 'Diterima Penuh',
                        ])>
                            {{ strtoupper($status_penerimaan) }}
                        </div>
                    </div>
                </div>

                @if($status_penerimaan !== 'Diterima Penuh')
                <div class="mb-10 p-5 bg-red-50 border-2 border-dashed border-red-200 rounded-2xl flex items-center gap-6">
                    <div class="bg-red-100 p-4 rounded-full text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-800">Upload Bukti Kerusakan / Retur</p>
                        <p class="text-xs text-red-600 mb-2 italic">*Wajib diisi karena ada indikasi barang rusak/hilang</p>
                        <input type="file" wire:model="foto_bukti_rusak" class="text-xs text-gray-500">
                    </div>
                </div>
                @endif

                @if($id_pengiriman)
                <div class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm bg-white">
                    <table class="w-full">
                        <thead class="bg-gray-800 text-[10px] text-white font-bold uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 text-left">Info Material</th>
                                <th class="px-4 py-4 text-center">Qty DO</th>
                                <th class="px-4 py-4 text-center text-emerald-400">Kondisi Bagus</th>
                                <th class="px-6 py-4 text-center text-indigo-400">Lokasi Rak & Area</th>
                                <th class="px-4 py-4 text-center text-red-400">Kondisi Rusak</th>
                                <th class="px-6 py-4 text-left">Alasan Retur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($detailTerima as $key => $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $item['nama_material'] }}</div>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $item['kategori'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center font-black bg-gray-50/50">{{ $item['qty_dikirim'] }}</td>
                                <td class="px-4 py-4 w-28">
                                    <input type="number" wire:model.blur="detailTerima.{{ $key }}.jumlah_bagus" class="w-full border-gray-200 rounded-lg text-center font-bold text-emerald-700 bg-emerald-50 focus:ring-emerald-500">
                                </td>
                                <td class="px-6 py-4 w-72">
                                    @if($item['jumlah_bagus'] > 0)
                                    <select wire:model="detailTerima.{{ $key }}.id_lokasi" class="w-full border-gray-200 rounded-lg text-xs font-bold py-2 bg-indigo-50">
                                        <option value="">-- Pilih Penempatan --</option>
                                        @foreach($listRak as $rak)
                                            <option value="{{ $rak->id_lokasi }}">
                                                {{ $rak->nama_lokasi }} (Area: {{ $rak->area }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("detailTerima.$key.id_lokasi") <span class="text-[9px] text-red-600 font-bold mt-1 block">Wajib pilih rak!</span> @enderror
                                    @else
                                    <div class="text-center text-[10px] text-gray-400 italic">No storage needed</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 w-28">
                                    <input type="number" wire:model.blur="detailTerima.{{ $key }}.jumlah_rusak" class="w-full border-gray-200 rounded-lg text-center font-bold text-red-700 bg-red-50 focus:ring-red-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" wire:model.blur="detailTerima.{{ $key }}.alasan_return" placeholder="Catatan..." class="w-full border-gray-200 rounded-lg text-xs italic">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed">
                    <p class="text-gray-400 font-bold">Silahkan pilih Nomor DO terlebih dahulu untuk memuat data material.</p>
                </div>
                @endif
            </div>

            <div class="p-6 border-t bg-white flex justify-end gap-4 shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                <button wire:click="$set('isModalOpen', false)" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-800 transition uppercase tracking-widest">Batal</button>
                <button wire:click="store" class="px-10 py-2.5 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:bg-indigo-700 flex items-center gap-3 transition-all">
                    <div wire:loading wire:target="store" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                    Simpan & Update Stok FIFO
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- STYLE SEKARANG DI DALAM DIV ROOT --}}
    <style>
        @keyframes modal-up {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-modal-up { animation: modal-up 0.3s ease-out; }
    </style>
</div> {{-- AKHIR DIV ROOT --}}