<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Proyek Saya</h1>
            <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">Daftar Penugasan Proyek dari Manajer</p>
        </div>
    </div>

    {{-- Toolbar: Search & Filter --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="relative w-full md:w-1/2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau ID Proyek..." class="w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl focus:ring-indigo-500 text-sm font-medium bg-gray-50">
        </div>
        
        <div class="w-full md:w-64">
            <select wire:model.live="filterStatus" class="w-full py-3 border-gray-200 rounded-xl focus:ring-indigo-500 text-sm font-medium bg-gray-50">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>
    </div>

    {{-- Grid Layout untuk Penugasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($listPenugasan as $item)
        <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col">
            
            {{-- Header Card --}}
            <div class="p-6 border-b border-gray-50 relative">
                <div class="absolute top-6 right-6">
                    @if($item->status_penugasan == 'Aktif')
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-xs font-black">Aktif</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-xs font-black">Nonaktif</span>
                    @endif
                </div>
                
                <h3 class="text-xl font-extrabold text-gray-800 pr-16 mb-1 line-clamp-2" title="{{ $item->proyek->nama_proyek ?? 'Proyek Tidak Tersedia' }}">
                    {{ $item->proyek->nama_proyek ?? 'Proyek Tidak Tersedia' }}
                </h3>
                <p class="text-xs text-indigo-500 font-bold uppercase tracking-wider">
                    ID: {{ $item->id_proyek }}
                </p>
            </div>

            {{-- Body Card --}}
            <div class="p-6 flex-grow flex flex-col gap-4">
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-50">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Peran Anda</p>
                    <p class="text-sm font-bold text-gray-800">{{ $item->peran_proyek ?? '-' }}</p>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Tgl Mulai</p>
                        <p class="text-xs font-bold text-gray-800">
                            {{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div class="text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Tgl Selesai</p>
                        <p class="text-xs font-bold text-gray-800">
                            {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer Card --}}
            <div class="p-4 border-t border-gray-50 bg-gray-50/50">
                
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
            <div class="bg-gray-50 p-6 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Penugasan</h3>
            <p class="text-sm text-gray-500 max-w-sm">Anda belum ditugaskan ke proyek manapun saat ini. Silakan hubungi Manajer Proyek Anda jika ini sebuah kesalahan.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $listPenugasan->links() }}
    </div>
</div>