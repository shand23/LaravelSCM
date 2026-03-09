<div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Permintaan Material Proyek</h1>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Buat Permintaan
        </button>
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

    {{-- FILTER & SEARCH --}}
    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID atau Nama Proyek..." 
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider select-none">
                        <div class="flex items-center space-x-1">
                            <span>ID Permintaan</span>
                            @if($sortColumn === 'id_permintaan')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Proyek</th>
                    <th wire:click="sortBy('tanggal_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider select-none">
                        <div class="flex items-center space-x-1">
                            <span>Tgl Permintaan</span>
                            @if($sortColumn === 'tanggal_permintaan')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($permintaans as $p)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            {{ $p->id_permintaan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->proyek->nama_proyek }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($p->tanggal_permintaan)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($p->status_permintaan) {
                                    'Menunggu Persetujuan' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                    'Disetujui PM' => 'bg-green-100 text-green-800 border border-green-200',
                                    'Ditolak' => 'bg-red-100 text-red-800 border border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                };
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                {{ $p->status_permintaan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <button wire:click="show('{{ $p->id_permintaan }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200 inline-block mx-1">
                                Detail
                            </button>
                            
                            {{-- LOGIKA TOMBOL EDIT & DELETE MUNCUL JIKA STATUS MASIH MENUNGGU PERSETUJUAN --}}
                            @if($p->status_permintaan === 'Menunggu Persetujuan')
                                <button wire:click="edit('{{ $p->id_permintaan }}')" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded-md border border-yellow-200 inline-block mx-1">
                                    Edit
                                </button>
                                <button wire:click="delete('{{ $p->id_permintaan }}')" 
                                        wire:confirm="Yakin ingin menghapus permintaan material ini?" 
                                        class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md border border-red-200 inline-block mx-1">
                                    Hapus
                                </button>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center italic">
                            Belum ada data permintaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $permintaans->links() }}
        </div>
    </div>

    {{-- MODAL CREATE / EDIT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit Permintaan Material' : 'Buat Permintaan Baru' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Proyek</label>
                            <select wire:model.live="id_proyek" class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500" {{ $isEditMode ? 'disabled' : '' }}>
                                <option value="">-- Pilih Proyek --</option>
                                @foreach($listProyek as $pro) 
                                    <option value="{{ $pro->id_proyek }}">{{ $pro->nama_proyek }}</option> 
                                @endforeach
                            </select>
                            @error('id_proyek') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Dibutuhkan</label>
                            <input type="date" wire:model="tanggal_permintaan" 
                                   @if(!$isEditMode) min="{{ date('Y-m-d') }}" @endif
                                   @if($batas_tanggal) max="{{ $batas_tanggal }}" @endif
                                   class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            
                            @if($batas_tanggal)
                                <p class="text-[11px] text-gray-500 mt-1 font-medium">
                                    Batas maksimal: <span class="text-blue-600 font-bold">{{ \Carbon\Carbon::parse($batas_tanggal)->format('d M Y') }}</span>
                                </p>
                            @endif
                            @error('tanggal_permintaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between items-end border-b pb-2">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Daftar Material</label>
                        <button type="button" wire:click="addItem" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-3 py-1 rounded border border-blue-200">+ TAMBAH BARIS</button>
                    </div>

                    <div class="space-y-3 max-h-[40vh] overflow-y-auto p-1">
                        @foreach($items as $index => $item)
                        <div class="flex gap-4 items-start bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label class="text-[10px] uppercase font-bold text-gray-500 block mb-1">Pilih Material</label>
                                <select wire:model.live="items.{{ $index }}.id_material" class="w-full border-gray-300 rounded-md sm:text-sm border px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($listMaterial as $mat) 
                                        <option value="{{ $mat->id_material }}">
                                            {{ $mat->nama_material }} ({{ $mat->satuan ?? '-' }})
                                        </option> 
                                    @endforeach
                                </select>
                                @error("items.$index.id_material") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="w-48">
                                <label class="text-[10px] uppercase font-bold text-gray-500 block mb-1">Jumlah</label>
                                <div class="flex">
                                    <input type="number" min="1" wire:model="items.{{ $index }}.jumlah_diminta" class="w-full border-gray-300 rounded-l-md sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-center">
                                    
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-200 text-gray-600 sm:text-sm font-bold whitespace-nowrap">
                                        @php
                                            $selectedMat = $listMaterial->firstWhere('id_material', $item['id_material']);
                                            echo $selectedMat ? ($selectedMat->satuan ?? '-') : '-';
                                        @endphp
                                    </span>
                                </div>
                                @error("items.$index.jumlah_diminta") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            @if(count($items) > 1)
                            <div class="pt-6">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-white hover:bg-red-500 font-bold p-2 rounded transition-colors" title="Hapus Baris">
                                    ✕
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end space-x-3 mt-8 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL TETAP SAMA SEPERTI SEBELUMNYA --}}
    @if($isDetailOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Detail Permintaan: <span class="text-blue-600">{{ $selectedPermintaan->id_permintaan }}</span>
                </h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Proyek</p>
                        <p class="font-bold text-gray-800">{{ $selectedPermintaan->proyek->nama_proyek }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Status</p>
                        <p class="font-bold text-gray-800">{{ $selectedPermintaan->status_permintaan }}</p>
                    </div>
                </div>
                
                <h3 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Daftar Material Diminta</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Satuan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($selectedPermintaan->detailPermintaan as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $detail->material->nama_material }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-500 font-medium bg-gray-50">{{ $detail->material->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-blue-600">{{ $detail->jumlah_diminta }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-end mt-8">
                    <button wire:click="closeModal" class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors">Tutup Jendela</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>