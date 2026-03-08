<div>
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Master Data: Kategori Material</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Tambah Kategori
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..." class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
    </div>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($kategoris as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->id_kategori_material }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $item->nama_kategori }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->deskripsi ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item->status_kategori == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status_kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit('{{ $item->id_kategori_material }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="delete('{{ $item->id_kategori_material }}')" wire:confirm="Hapus kategori ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $kategoris->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="bg-gray-100 px-4 py-3 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h2>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" wire:model="nama_kategori" placeholder="Contoh: Semen, Besi, Cat..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                        @error('nama_kategori') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Penjelasan singkat kategori ini..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2"></textarea>
                        @error('deskripsi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Kategori</label>
                        <select wire:model="status_kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        @error('status_kategori') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>