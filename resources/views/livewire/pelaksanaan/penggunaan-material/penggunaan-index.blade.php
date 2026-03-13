<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Pelaksanaan: Laporan Penggunaan</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Realisasi Pemasangan Material di Lapangan</p>
        </div>
        <button wire:click="openModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Laporan Baru
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-xl font-bold border border-emerald-200 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Tabel Utama Laporan Penggunaan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b bg-white">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID Laporan atau Area Pekerjaan..." class="w-full md:w-96 px-4 py-2.5 border-gray-200 rounded-xl focus:ring-indigo-500 text-sm font-medium bg-gray-50">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-[10px] font-black text-gray-300 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">ID Laporan & Tgl</th>
                        <th class="px-6 py-4">Ref. Permintaan</th>
                        <th class="px-6 py-4">Proyek & Area</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($listLaporan as $laporan)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $laporan->id_penggunaan }}</div>
                            <div class="text-[11px] text-gray-500 font-bold mt-1">{{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded text-xs font-black border border-blue-200">{{ $laporan->id_permintaan }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $laporan->proyek->nama_proyek ?? '-' }}</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase mt-1">Area: {{ $laporan->area_pekerjaan }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-xs">{{ $laporan->keterangan_umum ?: '-' }}</td>
                        <td class="px-6 py-4">
                            <button class="text-indigo-600 hover:text-indigo-900 font-bold text-xs underline">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-400 font-bold">Belum ada data laporan penggunaan material.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50 rounded-b-2xl">{{ $listLaporan->links() }}</div>
    </div>

    {{-- MODAL FORM LAPORAN --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl my-8">
            
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center rounded-t-3xl">
                <h3 class="text-xl font-extrabold text-white">Buat Laporan Penggunaan Lapangan</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white bg-gray-800 p-2 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Pilih Request --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pilih Referensi Permintaan (Request)</label>
                        <select wire:model.live="id_permintaan_selected" class="w-full py-2.5 border-gray-300 rounded-xl focus:ring-indigo-500 text-sm font-medium">
                            <option value="">-- Pilih Nomor Request --</option>
                            @foreach($daftarPermintaan as $req)
                                <option value="{{ $req->id_permintaan }}">{{ $req->id_permintaan }} - Proyek: {{ $req->proyek->nama_proyek ?? '-' }}</option>
                            @endforeach
                        </select>
                        @error('id_permintaan_selected') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tanggal Laporan</label>
                        <input type="date" wire:model="tanggal_laporan" class="w-full py-2.5 border-gray-300 rounded-xl focus:ring-indigo-500 text-sm font-medium">
                        @error('tanggal_laporan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Area --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Area Pekerjaan (Cth: Zona A Lantai 2)</label>
                        <input type="text" wire:model="area_pekerjaan" class="w-full py-2.5 border-gray-300 rounded-xl focus:ring-indigo-500 text-sm font-medium">
                        @error('area_pekerjaan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Keterangan Umum</label>
                        <input type="text" wire:model="keterangan_umum" class="w-full py-2.5 border-gray-300 rounded-xl focus:ring-indigo-500 text-sm font-medium">
                    </div>
                </div>

                {{-- Tabel Detail Dinamis --}}
                @if(count($detailBarang) > 0)
                <div class="border rounded-xl overflow-hidden mb-4">
                    <div class="bg-amber-50 p-3 border-b border-amber-100">
                        <p class="text-xs text-amber-800 font-bold">Silakan isi kuantitas pemakaian riil untuk setiap material di bawah ini.</p>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-4 py-3">Material</th>
                                <th class="px-4 py-3 text-center w-24">Terkirim</th>
                                <th class="px-4 py-3 text-center w-28 text-emerald-600">Terpasang</th>
                                <th class="px-4 py-3 text-center w-28 text-red-600">Rusak</th>
                                <th class="px-4 py-3 text-center w-28 text-amber-600">Sisa</th>
                                <th class="px-4 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($detailBarang as $index => $item)
                            <tr>
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $item['nama_material'] }}</td>
                                <td class="px-4 py-3 text-center font-bold text-gray-500 bg-gray-50">{{ $item['jumlah_terkirim'] }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" wire:model="detailBarang.{{ $index }}.jumlah_terpasang_riil" class="w-full py-1.5 px-2 border-emerald-300 rounded text-center text-sm focus:ring-emerald-500" min="0">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" wire:model="detailBarang.{{ $index }}.jumlah_rusak_lapangan" class="w-full py-1.5 px-2 border-red-300 rounded text-center text-sm focus:ring-red-500" min="0">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" wire:model="detailBarang.{{ $index }}.jumlah_sisa_material" class="w-full py-1.5 px-2 border-amber-300 rounded text-center text-sm focus:ring-amber-500" min="0">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="detailBarang.{{ $index }}.catatan_khusus" class="w-full py-1.5 px-2 border-gray-300 rounded text-sm placeholder-gray-300" placeholder="Opsional...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    @if($id_permintaan_selected)
                        <div class="text-center py-8 text-gray-500 text-sm font-bold bg-gray-50 rounded-xl border border-dashed">Memuat detail material atau material kosong...</div>
                    @endif
                @endif
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-3xl">
                <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                <button wire:click="simpanLaporan" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition-all">Simpan Laporan Pemasangan</button>
            </div>

        </div>
    </div>
    @endif
</div>