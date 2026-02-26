<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Review Usulan Material</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola dan berikan persetujuan untuk material yang diajukan oleh Tim Proyek.</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="mb-6 relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input wire:model.live="search" type="text" placeholder="Cari ID Usulan, Nama Material, atau Nama Pengusul..." class="block w-full md:w-1/2 p-2.5 pl-10 text-sm text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm">
    </div>

    {{-- Table Data --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ID Request</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Material & Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($usulan_materials as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-bold text-purple-700 bg-purple-50 border border-purple-100 px-2.5 py-1 rounded-md">{{ $item->id_usulan_material }}</span>
                            <div class="text-xs text-gray-400 mt-1">{{ $item->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{-- MEMANGGIL NAMA LENGKAP --}}
                            <div class="text-sm font-bold text-gray-900">{{ $item->pengusul->nama_lengkap ?? 'User Dihapus' }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $item->id_user_pengusul }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $item->nama_material }} <span class="text-xs font-normal text-gray-500">({{ $item->satuan }})</span></div>
                            <div class="text-xs text-blue-600 font-medium mt-1">{{ $item->kategori->nama_kategori ?? '-' }}</div>
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
                                <div class="text-[11px] text-gray-500 mt-2 bg-gray-50 p-1.5 rounded border border-gray-100 max-w-[200px] truncate" title="{{ $item->catatan_admin }}">
                                    <span class="font-semibold">Catatan:</span> {{ $item->catatan_admin }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button wire:click="prosesUsulan('{{ $item->id_usulan_material }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 px-4 py-1.5 rounded-lg transition-all font-semibold shadow-sm">
                                Proses Review
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada usulan material masuk.</h3>
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

    {{-- MODAL PROSES (APPROVAL) --}}
    @if($isModalOpen && $detailUsulan)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Proses Usulan Material</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="px-6 pt-5 pb-6">
                    {{-- Informasi Detail (Read Only) --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 space-y-3 text-sm">
                        <div class="grid grid-cols-3 gap-2 border-b border-gray-200 pb-2">
                            <span class="text-gray-500 font-semibold">ID Request</span>
                            <span class="col-span-2 font-mono font-bold text-purple-700">{{ $detailUsulan->id_usulan_material }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 border-b border-gray-200 pb-2">
                            <span class="text-gray-500 font-semibold">Pengusul</span>
                            <span class="col-span-2 font-medium">{{ $detailUsulan->pengusul->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 border-b border-gray-200 pb-2">
                            <span class="text-gray-500 font-semibold">Material</span>
                            <span class="col-span-2 font-bold">{{ $detailUsulan->nama_material }} ({{ $detailUsulan->satuan }})</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="text-gray-500 font-semibold">Spesifikasi</span>
                            <span class="col-span-2 text-gray-700">{{ $detailUsulan->spesifikasi ?: 'Tidak ada detail spesifikasi yang disertakan.' }}</span>
                        </div>
                    </div>

                    {{-- Form Input (Bisa Diedit Admin) --}}
                    <form wire:submit.prevent="simpanProses">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Keputusan Status <span class="text-red-500">*</span></label>
                                <select wire:model="status_usulan" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Disetujui">Disetujui (Terima Usulan)</option>
                                    <option value="Ditolak">Ditolak (Tolak Usulan)</option>
                                </select>
                                @error('status_usulan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Admin (Opsional)</label>
                                <textarea wire:model="catatan_admin" rows="3" placeholder="Beri alasan jika ditolak, atau catatan jika disetujui..." class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Tim proyek dapat melihat catatan ini.</p>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="bg-white py-2 px-5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all">Batal</button>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-5 rounded-xl text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all">Simpan Keputusan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>