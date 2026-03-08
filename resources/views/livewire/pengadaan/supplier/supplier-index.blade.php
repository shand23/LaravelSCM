<div>
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Master Data: Supplier / Vendor</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Tambah Supplier
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama supplier atau kontak..." class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
    </div>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($suppliers as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->id_supplier }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $item->nama_supplier }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->kontak_person ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->no_telepon }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->status_supplier == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->status_supplier }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit('{{ $item->id_supplier }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete('{{ $item->id_supplier }}')" wire:confirm="Hapus data supplier ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $suppliers->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="bg-gray-100 px-4 py-3 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $isEditMode ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
                </h2>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="store">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Supplier / PT <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nama_supplier" placeholder="Contoh: PT Sumber Bangunan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('nama_supplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Kontak Person</label>
                            <input type="text" wire:model="kontak_person" placeholder="Contoh: Bpk. Budi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('kontak_person') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon/WA <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="no_telepon" placeholder="Contoh: 08123456789" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('no_telepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email" placeholder="contoh@domain.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="2" placeholder="Alamat kantor atau gudang supplier..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2"></textarea>
                        @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Supplier <span class="text-red-500">*</span></label>
                        <select wire:model="status_supplier" class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        @error('status_supplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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