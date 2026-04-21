<div wire:poll.3s class="p-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Lokasi Rak</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar gudang, rak, dan area penyimpanan</p>
        </div>
        
        {{-- TOMBOL TAMBAH DENGAN IZIN --}}
        @if(auth()->user()->can_manage_master)
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Lokasi
        </button>
        @endif
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

    {{-- SEARCH & FILTER --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-[300px]">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama rak atau area..." class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all text-sm">
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">ID Lokasi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Rak / Lokasi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Area</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                    
                    {{-- HEADER AKSI DENGAN IZIN --}}
                    @if(auth()->user()->can_manage_master)
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($listLokasi as $lokasi)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $lokasi->id_lokasi }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $lokasi->nama_lokasi }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $lokasi->AREA }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $lokasi->keterangan ?: '-' }}</td>
                        
                        {{-- TOMBOL AKSI DENGAN IZIN --}}
                        @if(auth()->user()->can_manage_master)
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button wire:click="edit('{{ $lokasi->id_lokasi }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="delete('{{ $lokasi->id_lokasi }}')" wire:confirm="Hapus lokasi rak ini?" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can_manage_master ? 5 : 4 }}" class="px-6 py-12 text-center text-gray-400 italic">
                            Data lokasi tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $listLokasi->links() }}
        </div>
    </div>

    {{-- MODAL FORM --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <div class="inline-block w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all align-middle">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $edit_id ? 'Update Lokasi Rak' : 'Tambah Lokasi Rak Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Rak / Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nama_lokasi" placeholder="Contoh: RAK-001" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        @error('nama_lokasi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Area <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="AREA" placeholder="Contoh: Kayu & Batu" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        @error('AREA') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea wire:model="keterangan" rows="3" placeholder="Opsional..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end p-5 border-t border-gray-100 bg-gray-50 gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="store" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>