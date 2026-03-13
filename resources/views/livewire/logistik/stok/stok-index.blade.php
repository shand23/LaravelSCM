<div class="p-6 bg-gray-50 min-h-screen relative">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Logistik: Monitor Stok</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Daftar Seluruh Material & Ketersediaan Stok</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b bg-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau ID Material..." class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-[10px] font-black text-gray-300 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg w-16 text-center">No</th>
                        <th class="px-6 py-4">Informasi Material</th>
                        <th class="px-6 py-4 text-center">Batch Terlama</th>
                        <th class="px-6 py-4 text-center">Status Gudang</th>
                        <th class="px-6 py-4 text-center">Total Stok Tersedia</th>
                        <th class="px-6 py-4 text-right rounded-tr-lg">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($listStok as $index => $stok)
                    <tr class="hover:bg-indigo-50/30 transition-colors {{ $stok->total_sisa == 0 ? 'bg-red-50/20' : '' }}">
                        <td class="px-6 py-4 text-center text-gray-500 font-bold">{{ $listStok->firstItem() + $index }}</td>
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $stok->nama_material }}</div>
                            <div class="flex gap-2 mt-1">
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">
                                    {{ $stok->id_material }}
                                </span>
                                <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-indigo-100">
                                    {{ $stok->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($stok->tgl_terlama)
                                <div class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($stok->tgl_terlama)->format('d M Y') }}</div>
                                <div class="text-[10px] text-amber-600 font-bold uppercase tracking-widest mt-0.5">Prioritas Keluar</div>
                            @else
                                <span class="text-xs font-black text-gray-400">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($stok->total_sisa == 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-black border border-red-200 animate-pulse">
                                    STOK HABIS
                                </span>
                            @else
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-black border border-blue-200">
                                    {{ $stok->jumlah_batch }} Batch
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div @class([
                                'text-2xl font-black',
                                'text-gray-800' => $stok->total_sisa > 20,
                                'text-amber-500' => $stok->total_sisa > 0 && $stok->total_sisa <= 20,
                                'text-red-600' => $stok->total_sisa == 0,
                            ])>
                                {{ number_format($stok->total_sisa, 0, ',', '.') }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Tombol Cek Rak (Hanya muncul jika stok > 0) --}}
                                @if($stok->total_sisa > 0)
                                    <button wire:click="lihatBatch('{{ $stok->id_material }}')" class="bg-indigo-100 hover:bg-indigo-600 text-indigo-700 hover:text-white px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        Rak
                                    </button>
                                @endif

                                {{-- Tombol Ajukan PR (Akan mengirim ID Material ke URL Pengajuan) --}}
                                <a href="{{ route('logistik.pengajuan', ['id_material' => $stok->id_material]) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-emerald-500/30 transition-all flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    PR
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <p class="text-sm font-bold">Data Master Material Kosong.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t bg-gray-50 rounded-b-2xl">
            {{ $listStok->links() }}
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL DETAIL BATCH FIFO (Kode ini tetap sama seperti sebelumnya) --}}
    {{-- ========================================== --}}
    @if($isModalBatchOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-500/20 p-2.5 rounded-xl text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-white">{{ $namaMaterialModal }}</h3>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-1">Kategori: {{ $kategoriMaterialModal }} • Total Stok: {{ $totalStokModal }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-red-500 p-2 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-amber-50 border-b border-amber-100 p-4 flex gap-3 items-center">
                <span class="bg-amber-200 text-amber-800 p-1.5 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                <p class="text-sm text-amber-800 font-semibold">Tabel di bawah telah diurutkan berdasarkan aturan <strong>FIFO (First In First Out)</strong>. Ambil barang dari baris paling atas (Urutan 1) terlebih dahulu!</p>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg text-center">Urutan Pengambilan</th>
                            <th class="px-4 py-3">ID Batch & Tgl Masuk</th>
                            <th class="px-4 py-3">Lokasi Penempatan</th>
                            <th class="px-4 py-3 text-center rounded-r-lg">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($detailBatches as $index => $batch)
                        <tr class="{{ $index === 0 ? 'bg-emerald-50/50' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="px-4 py-4 text-center">
                                @if($index === 0)
                                    <span class="bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-black shadow-sm animate-pulse">#1 AMBIL INI</span>
                                @else
                                    <span class="text-gray-400 font-bold">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800">{{ $batch->id_stok }}</div>
                                <div class="text-[11px] text-gray-500 font-semibold mt-0.5">Masuk: {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-indigo-700">{{ $batch->lokasiRak->nama_lokasi ?? 'BELUM DISET' }}</div>
                                <div class="text-[10px] text-indigo-400 font-black tracking-widest mt-0.5">AREA: {{ $batch->lokasiRak->area ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="text-xl font-black text-gray-800">{{ $batch->sisa_stok }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>