<div>
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Kelola User</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition-colors">
            + Tambah User
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $user->nama_lengkap }}
                                {{-- Badge penanda jika dia Admin Logistik --}}
                                @if($user->ROLE === 'Logistik' && $user->can_manage_master)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800" title="Admin Data Master">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"></path></svg>
                                        MASTER
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->ROLE == 'Admin' ? 'bg-purple-100 text-purple-800' : 
                                      ($user->ROLE == 'Logistik' ? 'bg-orange-100 text-orange-800' : 
                                      'bg-blue-100 text-blue-800') }}">
                                    {{ $user->ROLE }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->jabatan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status_user == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status_user }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <button wire:click="edit('{{ $user->id_user }}')" class="text-indigo-600 hover:text-indigo-900 mx-2 transition-colors">
                                    Edit
                                </button>

                                @if(auth()->user()->ROLE === 'Admin' && auth()->user()->id_user !== $user->id_user)
                                    <button wire:click="delete('{{ $user->id_user }}')"
                                            wire:confirm="Yakin ingin menghapus user ini?"
                                            class="text-red-600 hover:text-red-900 mx-2 transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $users->links() }} {{-- Pastikan Anda menampilkan pagination --}}
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT USER --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $user_id_to_edit ? 'Edit Data User' : 'Tambah User Baru' }}
                </h2>
            </div>

            <div class="p-6">
                {{-- Gunakan x-data untuk memantau perubahan pada input ROLE di client-side --}}
                <form wire:submit.prevent="store" x-data="{ currentRole: @entangle('ROLE') }">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="nama_lengkap" class="block w-full rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 px-3 py-2">
                        @error('nama_lengkap') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email" class="block w-full rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 px-3 py-2">
                        @error('email') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Role</label>
                            <select wire:model.live="ROLE" class="block w-full rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 px-3 py-2 bg-white">
                                <option value="">Pilih...</option>
                                <option value="Admin">Admin</option>
                                <option value="Tim Pengadaan">Tim Pengadaan</option>
                                <option value="Tim Pelaksanaan">Tim Pelaksanaan</option>
                                <option value="Logistik">Logistik</option>
                                <option value="Top Manajemen">Top Manajemen</option>
                            </select>
                            @error('ROLE') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                            <select wire:model="status_user" class="block w-full rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 px-3 py-2 bg-white">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                        <input type="text" wire:model="jabatan" placeholder="Opsional (Misal: Supervisor)" class="block w-full rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 px-3 py-2">
                    </div>
{{-- PENGATURAN CRUD MASTER: Hanya tampil jika ROLE = Logistik --}}
                    {{-- PERBAIKAN: Memindahkan @entangle ke dalam x-data agar tombol sinkron dengan Livewire --}}
                    <div x-show="currentRole === 'Logistik'" 
                         x-data="{ isMaster: @entangle('can_manage_master') }"
                         x-collapse x-cloak 
                         class="mb-5 p-4 bg-purple-50 rounded-xl border border-purple-100">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                {{-- PERBAIKAN: Gunakan x-model alih-alih wire:model --}}
                                <input type="checkbox" x-model="isMaster" class="sr-only">
                                <div class="w-10 h-5 bg-gray-300 rounded-full shadow-inner transition-colors duration-200" :class="{'bg-purple-300': isMaster}"></div>
                                <div class="dot absolute w-7 h-7 bg-white rounded-full shadow -left-1 -top-1 transition transform duration-200" :class="{'translate-x-full bg-purple-600': isMaster}"></div>
                            </div>
                            <div class="ml-3 text-purple-900 font-bold text-sm">
                                Izin Kelola Data Master
                            </div>
                        </label>
                        <p class="text-[10px] text-purple-600 mt-2 font-medium leading-tight">
                            *Jika diaktifkan, user ini akan bisa melakukan Tambah, Edit, dan Hapus pada menu Data Material, Kategori, & Lokasi Rak.
                        </p>
                    </div>

                    <div class="mb-6" x-data="{ showPassword: false }">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   wire:model="password" 
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 pr-10">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                                
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                
                                <svg x-cloak x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $user_id_to_edit ? '*Kosongkan jika tidak ingin mengganti password.' : '*Minimal 6 karakter.' }}</p>
                        @error('password') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-5 py-2.5 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition-colors">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>