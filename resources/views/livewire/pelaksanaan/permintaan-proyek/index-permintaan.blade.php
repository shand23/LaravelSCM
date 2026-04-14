<div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Permintaan Material Proyek</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition-colors font-semibold">
            + Buat Permintaan
        </button>
    </div>

    {{-- ALERT PESAN --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTER & SEARCH --}}
    <div class="bg-white p-4 mb-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID atau Nama Proyek..." 
                   class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2 bg-gray-50">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('id_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider select-none">
                            <div class="flex items-center space-x-1">
                                <span>ID Permintaan</span>
                                @if($sortColumn === 'id_permintaan')
                                    <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Proyek</th>
                        <th wire:click="sortBy('tanggal_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider select-none">
                            <div class="flex items-center space-x-1">
                                <span>Tgl Permintaan</span>
                                @if($sortColumn === 'tanggal_permintaan')
                                    <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($permintaans as $p)
                        <tr class="hover:bg-blue-50/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                {{ $p->id_permintaan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $p->proyek->nama_proyek ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($p->tanggal_permintaan)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($p->status_permintaan) {
                                        'Menunggu Persetujuan' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'Diproses Sebagian' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'Disetujui PM' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'Selesai' => 'bg-green-100 text-green-800 border border-green-200',
                                        'Ditolak' => 'bg-red-100 text-red-800 border border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                    };
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                    {{ $p->status_permintaan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- PERBAIKAN: Menggunakan flex agar tombol sejajar sempurna --}}
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="show('{{ $p->id_permintaan }}')" class="text-indigo-600 hover:text-white hover:bg-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200 transition-colors font-semibold">
                                        Detail
                                    </button>

                                    {{-- TOMBOL BARU: BUAT LAPORAN --}}
                                    @if(in_array($p->status_permintaan, ['Diproses Sebagian', 'Selesai']))
                                        {{-- PERBAIKAN: Menggunakan collect()->contains agar tidak error jika data berbentuk Collection --}}
                                        @if(!collect($usedPermintaanIds)->contains($p->id_permintaan))
                                            <a href="{{ route('pelaksanaan.penggunaan', ['id_permintaan' => $p->id_permintaan]) }}" 
                                               class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold transition-colors border border-emerald-600">
                                                Buat Laporan
                                            </a>
                                        @else
                                            <span class="bg-gray-100 text-gray-500 px-3 py-1.5 rounded-lg font-bold border border-gray-200">
                                                ✓ Dilaporkan
                                            </span>
                                        @endif
                                    @endif
                                    
                                    {{-- TOMBOL EDIT & DELETE JIKA STATUS MASIH MENUNGGU --}}
                                    @if($p->status_permintaan === 'Menunggu Persetujuan')
                                        <button wire:click="edit('{{ $p->id_permintaan }}')" class="text-amber-600 hover:text-white hover:bg-amber-500 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 transition-colors font-semibold">
                                            Edit
                                        </button>
                                        <button wire:click="delete('{{ $p->id_permintaan }}')" 
                                                wire:confirm="Yakin ingin menghapus permintaan material ini?" 
                                                class="text-red-600 hover:text-white hover:bg-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-200 transition-colors font-semibold">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="font-semibold">Belum ada data permintaan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $permintaans->links() }}
        </div>
    </div>

    {{-- MODAL CREATE / EDIT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit Permintaan Material' : 'Buat Permintaan Baru' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 rounded-lg p-1 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Pilih Proyek</label>
                            <select wire:model.live="id_proyek" class="w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 bg-white focus:ring-blue-500 focus:border-blue-500" {{ $isEditMode ? 'disabled' : '' }}>
                                <option value="">-- Pilih Proyek --</option>
                                @foreach($listProyek as $pro) 
                                    <option value="{{ $pro->id_proyek }}">{{ $pro->nama_proyek }}</option> 
                                @endforeach
                            </select>
                            @error('id_proyek') <span class="text-red-500 text-xs mt-1 font-bold block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Tanggal Dibutuhkan</label>
                            <input type="date" wire:model="tanggal_permintaan" 
                                   @if(!$isEditMode) min="{{ date('Y-m-d') }}" @endif
                                   @if($batas_tanggal) max="{{ $batas_tanggal }}" @endif
                                   class="w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
                            
                            @if($batas_tanggal)
                                <p class="text-[11px] text-gray-500 mt-1.5 font-medium">
                                    Batas maksimal: <span class="text-blue-600 font-bold">{{ \Carbon\Carbon::parse($batas_tanggal)->format('d M Y') }}</span>
                                </p>
                            @endif
                            @error('tanggal_permintaan') <span class="text-red-500 text-xs mt-1 font-bold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between items-end border-b pb-2">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Daftar Material</label>
                        <button type="button" wire:click="addItem" class="text-blue-600 hover:text-white hover:bg-blue-600 text-xs font-bold bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors">+ TAMBAH BARIS</button>
                    </div>

                    <div class="space-y-3 max-h-[40vh] overflow-y-auto p-1 custom-scrollbar">
                        @foreach($items as $index => $item)
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <div class="flex-1 w-full">
                                <label class="text-[10px] uppercase font-bold text-gray-500 block mb-1">Pilih Material</label>
                                <select wire:model.live="items.{{ $index }}.id_material" class="w-full border-gray-300 rounded-lg sm:text-sm border px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($listMaterial as $mat) 
                                        <option value="{{ $mat->id_material }}">
                                            {{ $mat->nama_material }} ({{ $mat->satuan ?? '-' }})
                                        </option> 
                                    @endforeach
                                </select>
                                @error("items.$index.id_material") <span class="text-red-500 text-xs mt-1 font-bold block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="w-full sm:w-48">
                                <label class="text-[10px] uppercase font-bold text-gray-500 block mb-1">Jumlah</label>
                                <div class="flex">
                                    <input type="number" min="1" wire:model="items.{{ $index }}.jumlah_diminta" class="w-full border-gray-300 rounded-l-lg sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-center font-bold text-blue-700">
                                    
                                    <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-200 text-gray-600 sm:text-sm font-bold whitespace-nowrap">
                                        {{-- PERBAIKAN: Dibungkus collect() agar kebal error --}}
                                        @php
                                            $selectedMat = collect($listMaterial)->firstWhere('id_material', $item['id_material']);
                                            echo $selectedMat ? ($selectedMat->satuan ?? '-') : '-';
                                        @endphp
                                    </span>
                                </div>
                                @error("items.$index.jumlah_diminta") <span class="text-red-500 text-xs mt-1 font-bold block">{{ $message }}</span> @enderror
                            </div>
                            
                            @if(count($items) > 1)
                            <div class="pt-0 sm:pt-5 w-full sm:w-auto flex justify-end">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-white hover:bg-red-500 bg-red-50 border border-red-200 font-bold p-2 rounded-lg transition-colors" title="Hapus Baris">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-5 border-t bg-white">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold px-6 py-2.5 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow transition-colors">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL --}}
    @if($isDetailOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{-- PERBAIKAN: Menggunakan null-safe operator ?-> --}}
                    Detail Permintaan: <span class="text-blue-600">{{ $selectedPermintaan?->id_permintaan }}</span>
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 rounded-lg p-1 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6">
                {{-- Cek jika data ada untuk mencegah error saat animasi close --}}
                @if($selectedPermintaan)
                <div class="grid grid-cols-2 gap-4 mb-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Proyek Target</p>
                        <p class="font-bold text-blue-900">{{ $selectedPermintaan->proyek->nama_proyek ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Status Peninjauan</p>
                        <p class="font-bold text-gray-800">{{ $selectedPermintaan->status_permintaan }}</p>
                    </div>
                </div>
                
                <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Daftar Material Diminta
                </h3>
                <div class="border border-gray-200 rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nama Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Satuan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($selectedPermintaan->detailPermintaan as $detail)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ $detail->material->nama_material ?? 'Material Dihapus' }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-500 font-semibold bg-gray-50/50">{{ $detail->material->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-black text-blue-600">{{ $detail->jumlah_diminta }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                
                <div class="flex justify-end mt-6 pt-4 border-t">
                    <button wire:click="closeModal" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-6 py-2.5 rounded-lg shadow transition-colors">Tutup Jendela</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>