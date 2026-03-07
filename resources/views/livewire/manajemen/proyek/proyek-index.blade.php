<div>
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Kelola Proyek</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Tambah Proyek
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if ($overdueCount > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 shadow-sm rounded-r-lg flex items-center" role="alert">
            <svg class="h-6 w-6 text-yellow-500 mr-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p>
                <span class="font-bold">Peringatan!</span> Terdapat <strong>{{ $overdueCount }}</strong> proyek yang sudah melewati tenggat waktu (tanggal selesai) namun statusnya belum diubah menjadi "Selesai".
            </p>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($proyeks as $proyek)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $proyek->id_proyek }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $proyek->nama_proyek }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $proyek->lokasi_proyek ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>
                                {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') : 'TBA' }} 
                                - 
                                {{ $proyek->tanggal_selesai ? \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d M Y') : 'TBA' }}
                            </div>
                            
                            @if($proyek->tanggal_selesai && \Carbon\Carbon::parse($proyek->tanggal_selesai)->isBefore(\Carbon\Carbon::today()) && $proyek->status_proyek != 'Selesai')
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        ⚠️ Melewati Tenggat
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $proyek->status_proyek == 'Aktif' ? 'bg-green-100 text-green-800' : ($proyek->status_proyek == 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $proyek->status_proyek }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            
                            @if($proyek->status_proyek != 'Selesai' && $proyek->tanggal_selesai && \Carbon\Carbon::parse($proyek->tanggal_selesai)->isBefore(\Carbon\Carbon::today()))
                                <button wire:click="markAsSelesai('{{ $proyek->id_proyek }}')"
                                        wire:confirm="Yakin ingin menandai proyek ini sebagai selesai?"
                                        class="text-green-600 hover:text-green-900 font-bold mr-3">
                                    ✓ Selesai
                                </button>
                            @endif

                            <button wire:click="edit('{{ $proyek->id_proyek }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Edit
                            </button>
                            <button wire:click="delete('{{ $proyek->id_proyek }}')"
                                    wire:confirm="Yakin ingin menghapus proyek ini secara permanen?"
                                    class="text-red-600 hover:text-red-900">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada data proyek.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $proyeks->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            
            <div class="bg-gray-100 px-4 py-3 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $isEditMode ? 'Edit Data Proyek' : 'Tambah Proyek Baru' }}
                </h2>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="store">
                    
                    @if($isEditMode)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">ID Proyek</label>
                            <input type="text" value="{{ $id_proyek }}" disabled class="mt-1 block w-full bg-gray-100 text-gray-500 border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 cursor-not-allowed">
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Proyek</label>
                        <input type="text" wire:model="nama_proyek" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
                        @error('nama_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Lokasi Proyek</label>
                        <input type="text" wire:model="lokasi_proyek" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
                        @error('lokasi_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" wire:model.live="tanggal_mulai" 
                                   @if(!$isEditMode) min="{{ date('Y-m-d') }}" @endif
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
                            @error('tanggal_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" wire:model="tanggal_selesai" 
                                   min="{{ $tanggal_mulai ?: ($isEditMode ? '' : date('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
                            @error('tanggal_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea wire:model="deskripsi_proyek" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2"></textarea>
                        @error('deskripsi_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Proyek</label>
                        <select wire:model="status_proyek" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2 bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditunda">Ditunda</option>
                        </select>
                        @error('status_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>