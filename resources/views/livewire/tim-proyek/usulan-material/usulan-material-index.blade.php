<div class="p-6">
    {{-- Header Content --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Usulan Material Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar permintaan penambahan material baru ke sistem logistik.</p>
        </div>
        <button wire:click="create" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ajukan Material Baru
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="mb-6">
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live="search" type="text" placeholder="Cari Usulan Nama Material..." class="block w-full p-2.5 pl-10 text-sm text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm">
        </div>
    </div>

    {{-- Table Data --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Request</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Material</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($usulan_materials as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-bold text-purple-700 bg-purple-50 border border-purple-100 px-2.5 py-1 rounded-md">{{ $item->id_usulan_material }}</span>
                            <div class="text-xs text-gray-400 mt-1">{{ $item->created_at->format('d M Y - H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 font-medium">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $item->nama_material }}</div>
                            <div class="text-xs text-gray-600 mt-0.5">Satuan: <span class="font-semibold">{{ $item->satuan }}</span></div>
                            @if($item->spesifikasi)
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2" title="{{ $item->spesifikasi }}">{{ $item->spesifikasi }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status_usulan == 'Menunggu')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-600"></span> Menunggu
                                </span>
                            @elseif($item->status_usulan == 'Disetujui')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Ditolak
                                </span>
                            @endif
                            
                            @if($item->catatan_admin)
                                <div class="text-[11px] text-gray-500 mt-2 bg-gray-50 p-1.5 rounded border border-gray-100 max-w-xs">
                                    <span class="font-semibold">Catatan Admin:</span> {{ $item->catatan_admin }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($item->status_usulan == 'Menunggu')
                                <button wire:click="edit('{{ $item->id_usulan_material }}')" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors">Edit</button>
                                <button wire:click="delete('{{ $item->id_usulan_material }}')" wire:confirm="Anda yakin ingin membatalkan dan menghapus usulan ini?" class="text-red-600 hover:text-red-900 transition-colors">Batal</button>
                            @else
                                <span class="text-gray-400 italic text-xs bg-gray-100 px-2 py-1 rounded">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada usulan</h3>
                            <p class="mt-1 text-sm text-gray-500">Anda belum membuat usulan material apapun.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usulan_materials->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $usulan_materials->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL FORM --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            {{-- Modal Panel --}}
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                {{-- Modal Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                        {{ $isEditMode ? 'Edit Usulan Material' : 'Form Pengajuan Material' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 pt-5 pb-6">
                    <form wire:submit.prevent="store">
                        <div class="space-y-5">
                            
                            {{-- Kategori Material --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Material <span class="text-red-500">*</span></label>
                                <select wire:model="id_kategori_material" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($daftar_kategori as $kat)
                                        <option value="{{ $kat->id_kategori_material }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                @error('id_kategori_material') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nama & Satuan (Grid) --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Material <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nama_material" placeholder="Misal: Cat Tembok Dulux" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                    @error('nama_material') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="satuan" placeholder="Pcs, Sak, dll" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                    @error('satuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Spesifikasi --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Spesifikasi Detail (Opsional)</label>
                                <textarea wire:model="spesifikasi" rows="3" placeholder="Jelaskan spesifikasi ukuran, warna, atau standar yang dibutuhkan..." class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5"></textarea>
                            </div>

                        </div>
                        
                        {{-- Modal Footer --}}
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="bg-white py-2 px-5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all">
                                Batal
                            </button>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-5 rounded-xl text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all">
                                {{ $isEditMode ? 'Simpan Perubahan' : 'Kirim Ajuan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>