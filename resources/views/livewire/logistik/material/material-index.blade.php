<div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Master Data: Material</h1>
        
        {{-- TOMBOL TAMBAH DENGAN IZIN --}}
        @if(auth()->user()->can_manage_master)
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition-colors">
                + Tambah Material
            </button>
        @endif
    </div>

    {{-- ALERT PESAN --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama material..." class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        
                        {{-- KOLOM AKSI DENGAN IZIN --}}
                        @if(auth()->user()->can_manage_master)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($materials as $material)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $material->id_material }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $material->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">{{ $material->nama_material }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $material->satuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $material->status_material == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $material->status_material }}
                            </span>
                        </td>
                        
                        {{-- TOMBOL AKSI DENGAN IZIN --}}
                        @if(auth()->user()->can_manage_master)
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button wire:click="edit('{{ $material->id_material }}')" class="text-indigo-600 hover:text-indigo-900 mx-1">Edit</button>
                            <button wire:click="delete('{{ $material->id_material }}')" wire:confirm="Yakin ingin menghapus material ini?" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can_manage_master ? 6 : 5 }}" class="px-6 py-8 text-center text-sm text-gray-500 italic">Data material tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $materials->links() }}
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Material' : 'Tambah Material Baru' }}</h2>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Kategori Material</label>
                            <select wire:model="id_kategori_material" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id_kategori_material }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('id_kategori_material') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Nama Material</label>
                            <input type="text" wire:model="nama_material" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('nama_material') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Satuan (Pcs, Kg, m, dll)</label>
                            <input type="text" wire:model="satuan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Status Material</label>
                            <select wire:model="status_material" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            @error('status_material') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700">Spesifikasi (Opsional)</label>
                        <textarea wire:model="spesifikasi" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700">Standar Kualitas (Opsional)</label>
                        <textarea wire:model="standar_kualitas" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded font-medium">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-bold">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>