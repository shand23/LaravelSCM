<div wire:poll.5s class="p-6 bg-gray-50 min-h-screen relative">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Logistik: Monitor Stok</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Daftar Seluruh Material & Ketersediaan Stok</p>
        </div>
    </div>

    {{-- ALERT PESAN SUKSES / ERROR --}}
    @if (session()->has('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <p class="font-bold">Berhasil!</p></div>
            <p class="text-sm mt-1">{{ session('success') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <p class="font-bold">Terjadi Kesalahan</p></div>
            <p class="text-sm mt-1">{{ session('error') }}</p>
        </div>
    @endif

   {{-- TOOLBAR: SEARCH & FILTER --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
        
        {{-- Input Pencarian --}}
        <div class="relative w-full md:w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID / Nama Material..." 
                class="pl-10 form-control block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        {{-- Dropdown Filter Kategori --}}
        <div class="w-full md:w-1/4">
            <select wire:model.live="filter_kategori" 
                class="form-control block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm cursor-pointer">
                <option value="">-- Semua Kategori --</option>
                @foreach($listKategori as $kat)
                    {{-- Sesuaikan field 'id_kategori_material' dan 'nama_kategori' sesuai kolom di database Anda --}}
                    <option value="{{ $kat->id_kategori_material }}">{{ $kat->nama_kategori ?? $kat->id_kategori_material }}</option>
                @endforeach
            </select>
        </div>
        
    </div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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
                                @if($stok->total_sisa > 0)
                                    <button wire:click="lihatBatch('{{ $stok->id_material }}')" class="bg-indigo-100 hover:bg-indigo-600 text-indigo-700 hover:text-white px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        Rak & Lapor
                                    </button>
                                @endif

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
    {{-- MODAL DETAIL BATCH FIFO --}}
    {{-- ========================================== --}}
    @if($isModalBatchOpen)
    <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-500/20 p-2.5 rounded-xl text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-white">{{ $namaMaterialModal }}</h3>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-1">Kategori: {{ $kategoriMaterialModal }} • Total Stok Tersedia: {{ $totalStokModal }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-red-500 p-2 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-blue-50 border-b border-blue-100 p-4 flex gap-3 items-center">
                <span class="bg-blue-200 text-blue-800 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <p class="text-sm text-blue-800 font-semibold">Gunakan <strong>Edit/Pindah Rak</strong> untuk memindahkan stok (bisa sebagian/split). Gunakan <strong>Lapor</strong> untuk mencatat barang rusak.</p>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg text-center w-24">Urutan</th>
                            <th class="px-4 py-3">ID Batch & Tgl Masuk</th>
                            <th class="px-4 py-3">Lokasi Penempatan</th>
                            <th class="px-4 py-3 text-center">Sisa Stok</th>
                            <th class="px-4 py-3 text-right rounded-r-lg">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($detailBatches as $index => $batch)
                        <tr class="{{ $index === 0 ? 'bg-emerald-50/30' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="px-4 py-4 text-center">
                                @if($index === 0)
                                    <span class="bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-black shadow-sm animate-pulse whitespace-nowrap">#1 (PRIORITAS)</span>
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
                                {{-- PERBAIKAN: Menggabungkan properti AREA dan area --}}
                                <div class="text-[10px] text-indigo-400 font-black tracking-widest mt-0.5">AREA: {{ $batch->lokasiRak->AREA ?? $batch->lokasiRak->area ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="text-xl font-black text-gray-800">{{ $batch->sisa_stok }}</div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openPindahRak('{{ $batch->id_stok }}', '{{ $batch->id_material }}', '{{ $batch->id_lokasi }}', {{ $batch->sisa_stok }})" class="bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white border border-blue-200 hover:border-blue-500 px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg> Pindah Rak
                                    </button>
                                    
                                    <button wire:click="openAdjustment('{{ $batch->id_stok }}', '{{ $batch->id_material }}', {{ $batch->sisa_stok }})" class="bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 hover:border-red-500 px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Lapor
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODAL PINDAH RAK & SPLIT BATCH --}}
    {{-- ========================================== --}}
    @if($isModalPindahRakOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/75 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden animate-[fadeIn_0.1s_ease-in]">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Pindah Rak / Alokasi Ulang
                </h3>
                <button wire:click="closePindahRak" class="text-blue-200 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="submitPindahRak">
                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500 font-semibold">ID Batch Asal:</span>
                            <span class="font-bold text-gray-800">{{ $pindah_id_stok }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500 font-semibold">Material:</span>
                            <span class="font-bold text-indigo-600 uppercase">{{ $namaMaterialModal }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-amber-600 mt-2">
                            <span>*Mengisi jumlah kurang dari {{ $pindah_max_stok }} akan memecah data batch (Split).</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Pilih Rak Tujuan</label>
                        <select wire:model="pindah_id_lokasi_tujuan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">-- Pilih Rak Tujuan Baru --</option>
                            @foreach($listLokasiRak as $rak)
                                {{-- PERBAIKAN: Menggabungkan properti AREA dan area --}}
                                <option value="{{ $rak->id_lokasi }}">
                                    {{ $rak->nama_lokasi }} (Area: {{ $rak->AREA ?? $rak->area ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('pindah_id_lokasi_tujuan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Jumlah Dipindah <span class="text-blue-600">(Maks: {{ $pindah_max_stok }})</span>
                        </label>
                        <input type="number" wire:model="pindah_jumlah" min="1" max="{{ $pindah_max_stok }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        @error('pindah_jumlah') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" wire:click="closePindahRak" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                    
                    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                        <span wire:loading.remove wire:target="submitPindahRak">Simpan Perubahan</span>
                        <span wire:loading wire:target="submitPindahRak">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODAL FORM PENYESUAIAN (LAPOR RUSAK) TETAP SAMA --}}
    {{-- ========================================== --}}
    @if($isModalAdjustmentOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/75 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden animate-[fadeIn_0.1s_ease-in]">
            <div class="bg-red-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Form Penyesuaian / Lapor Masalah
                </h3>
                <button wire:click="closeAdjustment" class="text-red-200 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="submitAdjustment">
                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500 font-semibold">ID Batch:</span>
                            <span class="font-bold text-gray-800">{{ $adj_id_stok }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 font-semibold">Stok Saat Ini:</span>
                            <span class="font-bold text-indigo-600">{{ $adj_max_stok }} Pcs</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Jenis Penyesuaian</label>
                        <select wire:model="adj_jenis" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            <option value="Rusak">Barang Rusak</option>
                            <option value="Hilang">Barang Hilang</option>
                            <option value="Kadaluarsa">Barang Kadaluarsa</option>
                            <option value="Selisih Opname">Selisih Stock Opname</option>
                        </select>
                        @error('adj_jenis') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Jumlah Pengurangan Stok</label>
                        <input type="number" wire:model="adj_jumlah" min="1" max="{{ $adj_max_stok }}" placeholder="Berapa yang rusak/hilang?" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                        @error('adj_jumlah') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Keterangan / Alasan detail</label>
                        <textarea wire:model="adj_keterangan" rows="3" placeholder="Contoh: Kardus basah karena atap gudang bocor..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200"></textarea>
                        @error('adj_keterangan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Bukti Foto (Opsional)</label>
                        <input type="file" wire:model="adj_bukti_foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 cursor-pointer">
                        
                        <div wire:loading wire:target="adj_bukti_foto" class="text-xs text-amber-600 font-bold mt-2 animate-pulse flex items-center gap-1">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Sedang memproses gambar...
                        </div>

                        @if ($adj_bukti_foto)
                            <div class="mt-3 relative inline-block">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Preview Bukti:</span>
                                <img src="{{ $adj_bukti_foto->temporaryUrl() }}" class="h-32 object-cover rounded-xl border-2 border-dashed border-gray-300 p-1 shadow-sm">
                            </div>
                        @endif
                        @error('adj_bukti_foto') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" wire:click="closeAdjustment" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                    
                    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold shadow hover:bg-red-700 focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span wire:loading.remove wire:target="submitAdjustment">Simpan Penyesuaian</span>
                        <span wire:loading wire:target="submitAdjustment">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>