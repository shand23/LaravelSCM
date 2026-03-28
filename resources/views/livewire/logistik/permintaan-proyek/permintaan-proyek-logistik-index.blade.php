<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Logistik: Permintaan Proyek</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Daftar Request Material dari Tim Pelaksanaan</p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-xl font-bold border border-emerald-200 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Toolbar: Search & Filter --}}
        <div class="p-5 border-b bg-white flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID atau Nama Proyek..." class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl focus:ring-indigo-500 text-sm font-medium bg-gray-50">
            </div>
            
            <div class="w-full md:w-64">
                <select wire:model.live="filterStatus" class="w-full py-2.5 border-gray-200 rounded-xl focus:ring-indigo-500 text-sm font-medium bg-gray-50">
                    <option value="">Semua Status Approved</option>
                    <option value="Disetujui PM">Disetujui PM</option>
                    <option value="Diproses Sebagian">Diproses Sebagian</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-[10px] font-black text-gray-300 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg text-center">No</th>
                        <th class="px-6 py-4">Informasi Request</th>
                        <th class="px-6 py-4">Tujuan Proyek</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($listPermintaan as $index => $item)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-6 py-4 text-center text-gray-500 font-bold">{{ $listPermintaan->firstItem() + $index }}</td>
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $item->id_permintaan }}</div>
                            <div class="text-[11px] text-gray-500 font-bold mt-1">Tgl: {{ \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d M Y') }}</div>
                            <div class="text-[10px] text-indigo-500 font-bold mt-0.5">Oleh: {{ $item->user->nama_lengkap ?? 'nama_lengkap' }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $item->proyek->nama_proyek ?? 'Proyek Tidak Ditemukan' }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">ID: {{ $item->id_proyek }}</div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->status_permintaan == 'Disetujui PM')
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg text-xs font-black">Disetujui PM</span>
                            @elseif($item->status_permintaan == 'Diproses Sebagian')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-xs font-black">Proses Sebagian</span>
                            @elseif($item->status_permintaan == 'Selesai')
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-xs font-black">Selesai</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-black">{{ $item->status_permintaan }}</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button wire:click="lihatDetail('{{ $item->id_permintaan }}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition-all">
                                Detail & Proses
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-400 font-bold">Belum ada data permintaan material yang disetujui.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t bg-gray-50 rounded-b-2xl">
            {{ $listPermintaan->links() }}
        </div>
    </div>

    {{-- MODAL DETAIL & PROSES --}}
    @if($isModalOpen && $permintaanTerpilih)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden">
            
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-500/20 p-2 rounded-xl text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-white">Detail Permintaan: {{ $permintaanTerpilih->id_permintaan }}</h3>
                        <p class="text-gray-400 text-xs font-bold uppercase mt-1">Proyek: {{ $permintaanTerpilih->proyek->nama_proyek ?? '-' }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white bg-gray-800 p-2 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Material</th>
                            <th class="px-4 py-3 text-center">Jumlah Diminta</th>
                            <th class="px-4 py-3 text-center">Terkirim (Gudang)</th>
                            <th class="px-4 py-3 text-center rounded-r-lg">Kekurangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($detailBarang as $det)
                        @php $kurang = $det->jumlah_diminta - $det->jumlah_terkirim; @endphp
                        <tr>
                            <td class="px-4 py-4 font-bold text-gray-800">{{ $det->material->nama_material ?? 'Unknown' }}</td>
                            <td class="px-4 py-4 text-center font-bold">{{ $det->jumlah_diminta }}</td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-600">{{ $det->jumlah_terkirim }}</td>
                            <td class="px-4 py-4 text-center font-black {{ $kurang > 0 ? 'text-red-500' : 'text-gray-300' }}">
                                {{ $kurang > 0 ? $kurang : '✓ Lunas' }}
                            </td>
                        </tr>
                        
                     {{-- MENAMPILKAN RIWAYAT BATCH MENGGUNAKAN ARRAY --}}
@if(isset($riwayatBatch[$det->id_material]))
<tr class="bg-gray-50/50">
    <td colspan="4" class="px-4 py-3 border-b border-gray-100">
        <div class="ml-2 pl-3 border-l-2 border-indigo-300">
            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Alokasi Stok Gudang:</span>
            <ul class="mt-1 space-y-1">
                @foreach($riwayatBatch[$det->id_material] as $batch)
                <li class="text-xs text-gray-600 font-medium">
                    <span class="inline-block w-2 h-2 bg-indigo-400 rounded-full mr-1"></span>
                    ID Stok <strong class="text-gray-800">#{{ $batch['id_stok'] }}</strong> 
                    <span class="text-gray-400">(Tgl Masuk: {{ $batch['tanggal_masuk'] ? \Carbon\Carbon::parse($batch['tanggal_masuk'])->format('d M Y') : '-' }})</span> 
                    &rarr; Diambil: <strong class="text-emerald-600">{{ $batch['jumlah_diambil'] }} unit</strong>
                </li>
                @endforeach
            </ul>
        </div>
    </td>
</tr>
@endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-3xl">
                <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Tutup</button>
                
                @php
                    $masihAdaKurang = false;
                    foreach($detailBarang as $d) {
                        if ($d->jumlah_diminta - $d->jumlah_terkirim > 0) $masihAdaKurang = true;
                    }
                @endphp

                @if($masihAdaKurang && $permintaanTerpilih->status_permintaan != 'Selesai')
                <button wire:click="prosesPemenuhanStok" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md flex items-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="prosesPemenuhanStok">Proses Tarik Stok Gudang</span>
                    <span wire:loading wire:target="prosesPemenuhanStok">Memproses...</span>
                </button>
                @else
                <button disabled class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-500 rounded-xl shadow-md flex items-center gap-2 cursor-not-allowed opacity-80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Semua Telah Terpenuhi
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>