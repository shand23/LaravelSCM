<div wire:poll.10s>
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Manajemen Penugasan Tim</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Tambah Penugasan
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, proyek, atau peran..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
        </div>
        <div class="w-full md:w-auto flex space-x-2">
            <select wire:model.live="filterProyek" class="border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                <option value="">Semua Proyek</option>
                @foreach($daftarProyek as $proyek)
                    <option value="{{ $proyek->id_proyek }}">{{ $proyek->nama_proyek }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Selesai">Selesai</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id_penugasan')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID @if($sortColumn === 'id_penugasan') {!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!} @endif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota Tim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th wire:click="sortBy('peran_proyek')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peran @if($sortColumn === 'peran_proyek') {!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!} @endif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($penugasans as $tugas)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tugas->id_penugasan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $tugas->user->nama_lengkap ?? 'User Dihapus' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tugas->proyek->nama_proyek ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tugas->peran_proyek }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tugas->tanggal_mulai ? \Carbon\Carbon::parse($tugas->tanggal_mulai)->format('d M') : '-' }} s/d 
                            {{ $tugas->tanggal_selesai ? \Carbon\Carbon::parse($tugas->tanggal_selesai)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $tugas->status_penugasan == 'Aktif' ? 'bg-green-100 text-green-800' : ($tugas->status_penugasan == 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $tugas->status_penugasan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($tugas->status_penugasan != 'Selesai')
                                <button wire:click="markAsSelesai('{{ $tugas->id_penugasan }}')" class="text-green-600 hover:text-green-900 font-bold mr-3">✓ Selesai</button>
                            @endif
                            <button wire:click="edit('{{ $tugas->id_penugasan }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="delete('{{ $tugas->id_penugasan }}')" wire:confirm="Hapus penugasan ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data penugasan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $penugasans->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="bg-gray-100 px-4 py-3 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $isEditMode ? 'Edit Penugasan' : 'Tambah Penugasan Baru' }}
                </h2>
                @if($id_proyek && $proyek_tanggal_mulai)
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                        Info Proyek: {{ \Carbon\Carbon::parse($proyek_tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($proyek_tanggal_selesai)->format('d M Y') }}
                    </span>
                @endif
            </div>

            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Proyek (Wajib)</label>
                            <select wire:model.live="id_proyek" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                                <option value="">-- Pilih Proyek Terlebih Dahulu --</option>
                                @foreach($daftarProyek as $proyek)
                                    <option value="{{ $proyek->id_proyek }}">{{ $proyek->nama_proyek }}</option>
                                @endforeach
                            </select>
                            @error('id_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Tim Pelaksana</label>
                            <select wire:model="id_user" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                                <option value="">-- Pilih Anggota Tim --</option>
                                @foreach($daftarUser as $user)
                                    <option value="{{ $user->id_user }}">{{ $user->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            @error('id_user') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Peran di Proyek</label>
                        <input type="text" wire:model="peran_proyek" placeholder="Contoh: Frontend Dev, Surveyor..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
                        @error('peran_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai Penugasan</label>
                            <input type="date" wire:model.live="tanggal_mulai" 
                                   {{ !$id_proyek ? 'disabled' : '' }}
                                   min="{{ $proyek_tanggal_mulai }}" 
                                   max="{{ $proyek_tanggal_selesai }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 {{ !$id_proyek ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            @error('tanggal_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Selesai Penugasan</label>
                            <input type="date" wire:model="tanggal_selesai" 
                                   {{ !$id_proyek ? 'disabled' : '' }}
                                   min="{{ $tanggal_mulai ?: $proyek_tanggal_mulai }}" 
                                   max="{{ $proyek_tanggal_selesai }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 {{ !$id_proyek ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            @error('tanggal_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Penugasan</label>
                        <select wire:model="status_penugasan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                        @error('status_penugasan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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