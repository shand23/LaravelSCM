<div>
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pengiriman (Ekspedisi)</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau jadwal truk dan kurir dari Supplier</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Catat Pengiriman Baru
        </button>
    </div>

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="bg-white p-4 mb-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari No. Pengiriman atau No. PO..." 
                   class="pl-10 w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Kirim</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. PO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Berangkat</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estimasi Tiba</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($listPengiriman as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $p->id_pengiriman }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->kontrak->nomor_kontrak ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($p->tanggal_berangkat)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($p->estimasi_tanggal_tiba)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center text-sm">
                            @php
                                $statusClass = match($p->status_pengiriman) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Dalam Perjalanan' => 'bg-blue-100 text-blue-800',
                                    'Tiba di Lokasi' => 'bg-purple-100 text-purple-800',
                                    'Selesai' => 'bg-green-100 text-green-800',
                                    'Return & Kirim Ulang' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                {{ $p->status_pengiriman }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium flex justify-center items-center gap-2">
                            @if($p->status_pengiriman == 'Pending')
                                {{-- Tombol Kirim: Mengubah status pengiriman dan status kontrak sekaligus --}}
                                <button wire:click="markAsInTransit('{{ $p->id_pengiriman }}')" wire:confirm="Tandai truk sedang dalam perjalanan? Data PO juga akan diupdate otomatis." class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1.5 rounded-md border border-blue-100 transition" title="Tandai Dalam Perjalanan">
                                    Kirim 🚚
                                </button>
                                <button wire:click="edit('{{ $p->id_pengiriman }}')" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-2 py-1.5 rounded-md border border-yellow-100 transition">Edit</button>
                                <button wire:click="delete('{{ $p->id_pengiriman }}')" wire:confirm="Yakin hapus data ini?" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1.5 rounded-md border border-red-100 transition">Hapus</button>
                            @else
                                <span class="text-gray-400 text-xs italic bg-gray-100 px-2 py-1 rounded border border-gray-200">Terkunci (Dalam Proses)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data pengiriman truk/ekspedisi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($listPengiriman->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $listPengiriman->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL CREATE / EDIT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit Jadwal Pengiriman' : 'Catat Pengiriman Baru' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}" class="p-6">
                <div class="space-y-5">
                    
                    {{-- Pilihan Kontrak / PO --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Pilih PO (Kontrak) <span class="text-red-500">*</span></label>
                        {{-- Dropdown .live untuk memanggil hook updatedIdKontrak --}}
                        <select wire:model.live="id_kontrak" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white" {{ $isEditMode ? 'disabled' : '' }}>
                            <option value="">-- Pilih PO yang Disepakati --</option>
                            @foreach($listKontrak as $kontrak)
                                <option value="{{ $kontrak->id_kontrak }}">
                                    {{ $kontrak->nomor_kontrak }} (Tgl PO: {{ \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_kontrak') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tgl Berangkat <span class="text-red-500">*</span></label>
                            <input type="date" 
                                   wire:model.live="tanggal_berangkat" 
                                   min="{{ $min_tanggal_berangkat }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 {{ !$id_kontrak ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                   {{ !$id_kontrak ? 'disabled' : '' }}>
                            @error('tanggal_berangkat') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Estimasi Tiba <span class="text-red-500">*</span></label>
                            <input type="date" 
                                   wire:model.live="estimasi_tanggal_tiba" 
                                   min="{{ $tanggal_berangkat }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 {{ !$tanggal_berangkat ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                   {{ !$tanggal_berangkat ? 'disabled' : '' }}>
                            @error('estimasi_tanggal_tiba') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Info Supir & Kendaraan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Supir / Ekspedisi</label>
                            <input type="text" wire:model="nama_supir" placeholder="Cth: Budi / JNE Cargo" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('nama_supir') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Plat Kendaraan</label>
                            <input type="text" wire:model="plat_kendaraan" placeholder="Cth: B 1234 CD" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('plat_kendaraan') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- FOOTER MODAL --}}
                <div class="flex justify-end space-x-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-5 py-2.5 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Jadwal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>