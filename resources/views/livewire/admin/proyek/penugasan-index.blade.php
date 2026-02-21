<div class="p-6">
    
    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Penugasan Tim Proyek</h2>
            <p class="text-sm text-gray-500">Kelola anggota tim yang bertugas di setiap proyek.</p>
        </div>
        <button wire:click="create" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-purple-200 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Penugasan
        </button>
    </div>

    {{-- Flash Message (Success) --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-green-700 hover:text-green-900 focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Flash Message (Error) --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Filter Search --}}
    <div class="mb-6 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input wire:model.live="search" type="text" placeholder="Cari nama anggota atau proyek..." class="pl-10 w-full md:w-1/3 border-gray-300 rounded-xl shadow-sm focus:ring-purple-500 focus:border-purple-500 transition-all">
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Penugasan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Proyek</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Peran & Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($penugasans as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">{{ $item->id_penugasan }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs uppercase">
                                    {{ substr($item->user->nama_lengkap ?? 'U', 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->user->nama_lengkap ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ $item->proyek->nama_proyek ?? 'Proyek Terhapus' }}</div>
                            <div class="text-xs text-gray-500">
                                @if(isset($item->proyek->tanggal_mulai))
                                    {{ \Carbon\Carbon::parse($item->proyek->tanggal_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($item->proyek->tanggal_selesai)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-semibold">{{ $item->peran_proyek }}</div>
                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/y') }} s/d {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/y') : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->status_penugasan == 'Aktif')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 self-center"></span> Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                {{-- Tombol Edit --}}
                                <button wire:click="edit('{{ $item->id_penugasan }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-lg transition-colors">
                                    Edit
                                </button>

                                {{-- Logic Hapus: Hanya jika status 'Nonaktif' --}}
                                @if($item->status_penugasan == 'Nonaktif')
                                    <button wire:click="delete('{{ $item->id_penugasan }}')" 
                                            wire:confirm="Yakin ingin menghapus data ini permanen?"
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                @else
                                    {{-- Ikon Gembok jika Aktif --}}
                                    <span class="text-gray-400 cursor-not-allowed bg-gray-50 px-2 py-1 rounded" title="Status Aktif tidak bisa dihapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p>Belum ada data penugasan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $penugasans->links() }}
        </div>
    </div>

    {{-- MODAL FORM (Create / Edit) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Backdrop --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Content --}}
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                {{-- Header Modal --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                        {{ $isEditMode ? 'Edit Penugasan' : 'Tambah Penugasan Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="space-y-4">
                            {{-- Pilih Anggota Tim --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Anggota Tim</label>
                                <select wire:model="id_user" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id_user }}">{{ $u->nama_lengkap }} ({{ $u->role }})</option>
                                    @endforeach
                                </select>
                                @error('id_user') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            {{-- Pilih Proyek (PENTING: wire:model.live untuk trigger validasi tanggal) --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Proyek</label>
                                <select wire:model.live="id_proyek" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option value="">-- Pilih Proyek --</option>
                                    @foreach($proyeks as $p)
                                        <option value="{{ $p->id_proyek }}">{{ $p->nama_proyek }}</option>
                                    @endforeach
                                </select>
                                @error('id_proyek') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                
                                {{-- Helper Text Range Proyek --}}
                                @if($minDate && $maxDate)
                                    <div class="mt-2 text-xs text-blue-700 bg-blue-50 border border-blue-200 p-2 rounded flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>
                                            Batas Tanggal Proyek: <br>
                                            <span class="font-bold">{{ \Carbon\Carbon::parse($minDate)->format('d/m/Y') }}</span> s/d 
                                            <span class="font-bold">{{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }}</span>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Peran --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Peran / Jabatan</label>
                                <input type="text" wire:model="peran_proyek" placeholder="Contoh: Site Manager, Logistik" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                @error('peran_proyek') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            {{-- Grid Tanggal --}}
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Tgl Mulai --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Tgl Mulai Tugas
                                    </label>
                                    <input type="date" 
                                           wire:model.live="tanggal_mulai" 
                                           min="{{ $minDate }}" 
                                           max="{{ $maxDate }}"
                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                           @if(!$id_proyek) disabled @endif>
                                    @error('tanggal_mulai') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Tgl Selesai --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Tgl Selesai Tugas
                                    </label>
                                    <input type="date" 
                                           wire:model="tanggal_selesai" 
                                           min="{{ $tanggal_mulai ?? $minDate }}" 
                                           max="{{ $maxDate }}"
                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                           @if(!$id_proyek) disabled @endif>
                                    @error('tanggal_selesai') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Penugasan</label>
                                <select wire:model="status_penugasan" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hanya status Nonaktif yang dapat dihapus.</p>
                            </div>
                        </div>

                        {{-- Footer Modal --}}
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="bg-white py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Penugasan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>