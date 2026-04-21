<div wire:poll.10s>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Master Data: Kategori Material</h1>
        
        {{-- HANYA TAMPIL JIKA PUNYA IZIN --}}
        @if(auth()->user()->can_manage_master)
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition-colors">
                + Tambah Kategori
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

    {{-- FILTER SEARCH --}}
    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..." class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
    </div>

    {{-- TABLE --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    
                    {{-- HEADER AKSI HANYA TAMPIL JIKA PUNYA IZIN --}}
                    @if(auth()->user()->can_manage_master)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kategoris as $kategori)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $kategori->id_kategori_material }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $kategori->nama_kategori }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $kategori->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $kategori->status_kategori == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $kategori->status_kategori }}
                        </span>
                    </td>
                    
                    {{-- TOMBOL AKSI HANYA TAMPIL JIKA PUNYA IZIN --}}
                    @if(auth()->user()->can_manage_master)
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <button wire:click="edit('{{ $kategori->id_kategori_material }}')" class="text-indigo-600 hover:text-indigo-900 mx-2 transition-colors">Edit</button>
                        <button wire:click="delete('{{ $kategori->id_kategori_material }}')" wire:confirm="Yakin ingin menghapus ini?" class="text-red-600 hover:text-red-900 mx-2 transition-colors">Hapus</button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->can_manage_master ? 5 : 4 }}" class="px-6 py-8 text-center text-sm text-gray-500 italic">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $kategoris->links() }}
        </div>
    </div>

    {{-- MODAL FORM (HANYA BISA TERBUKA JIKA PUNYA IZIN KARENA DICEK DI PHP) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h2>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" wire:model="nama_kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm px-3 py-2 border">
                        @error('nama_kategori') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm px-3 py-2 border"></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                        <select wire:model="status_kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm px-3 py-2 border bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>