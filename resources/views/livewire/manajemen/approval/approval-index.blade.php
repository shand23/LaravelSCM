<div wire:poll.3s>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Approval Permintaan Material</h1>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- SEARCH & FILTER --}}
    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID Permintaan atau Proyek..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
        </div>
        <div class="w-full md:w-auto flex space-x-2">
            {{-- Filter Proyek --}}
            <select wire:model.live="filterProyek" class="border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                <option value="">Semua Proyek</option>
                @foreach($daftarProyek as $proyek)
                    <option value="{{ $proyek->id_proyek }}">{{ $proyek->nama_proyek }}</option>
                @endforeach
            </select>
            
            {{-- Filter Status --}}
            <select wire:model.live="filterStatus" class="border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white">
                <option value="">Semua Status</option>
                <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                <option value="Disetujui PM">Disetujui PM</option>
                <option value="Diproses Sebagian">Diproses Sebagian</option>
                <option value="Selesai">Selesai</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        ID Permintaan @if($sortColumn === 'id_permintaan') {!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!} @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengaju</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th wire:click="sortBy('tanggal_permintaan')" class="cursor-pointer hover:bg-gray-100 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Tanggal @if($sortColumn === 'tanggal_permintaan') {!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!} @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($dataPermintaan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->id_permintaan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
    <div class="text-sm font-semibold text-gray-900">
        {{ $item->user->nama_lengkap ?? 'User Tidak Ditemukan' }}
    </div>
    <div class="text-[11px] text-gray-500 font-medium tracking-wider">
        ID: {{ $item->id_user }}
    </div>
</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $item->proyek->nama_proyek ?? 'Tanpa Proyek' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item->status_permintaan == 'Menunggu Persetujuan' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $item->status_permintaan == 'Disetujui PM' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $item->status_permintaan == 'Selesai' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item->status_permintaan == 'Ditolak' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $item->status_permintaan == 'Diproses Sebagian' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ $item->status_permintaan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="lihatDetail('{{ $item->id_permintaan }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                            
                            {{-- Sembunyikan tombol Approve langsung jika statusnya bukan Menunggu Persetujuan --}}
                            @if($item->status_permintaan == 'Menunggu Persetujuan')
                                <button wire:click="approve('{{ $item->id_permintaan }}')" onclick="confirm('Yakin ingin menyetujui permintaan ini?') || event.stopImmediatePropagation()" class="text-green-600 hover:text-green-900 font-bold">Approve</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data permintaan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- PAGINATION --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $dataPermintaan->links() }}
        </div>
    </div>

    {{-- MODAL DETAIL PERMINTAAN --}}
    @if($isModalOpen && $permintaanDipilih)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="bg-gray-100 px-4 py-3 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">
                    Detail Permintaan #{{ $permintaanDipilih->id_permintaan }}
                </h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-gray-800 font-bold text-xl leading-none">&times;</button>
            </div>

            <div class="p-6">
                <div class="mb-4 grid grid-cols-2 gap-4 bg-gray-50 p-3 rounded-md border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Proyek</p>
                        <p class="text-sm font-medium text-gray-900">{{ $permintaanDipilih->proyek->nama_proyek ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Tanggal Permintaan</p>
                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($permintaanDipilih->tanggal_permintaan)->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Tampilkan Catatan Penolakan Jika Ada (Saat Filter Ditolak) --}}
                @if($permintaanDipilih->status_permintaan == 'Ditolak' && $permintaanDipilih->catatan_penolakan)
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-md text-sm text-red-800">
                        <span class="font-bold block mb-1">Catatan Penolakan Sebelumnya:</span>
                        {{ $permintaanDipilih->catatan_penolakan }}
                    </div>
                @endif

                <h4 class="font-medium text-gray-700 mb-2 text-sm">Daftar Material yang Diminta:</h4>
                <div class="border border-gray-200 rounded-md overflow-hidden mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Diminta</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($permintaanDipilih->detailPermintaan as $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $detail->material->nama_material ?? 'Material Tidak Ditemukan' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $detail->jumlah_diminta }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-center text-sm text-gray-500">Detail tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- AREA FOOTER MODAL (AKSI & FORM PENOLAKAN) --}}
                <div class="mt-6 pt-4 border-t border-gray-100">
                    
                    @if($isRejecting)
                        {{-- Form Input Catatan Penolakan --}}
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <label class="block text-sm font-semibold text-red-700 mb-2">Alasan Penolakan untuk Tim Pelaksana:</label>
                            <textarea 
                                wire:model="catatan_penolakan" 
                                rows="3" 
                                class="w-full border-red-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2"
                                placeholder="Tuliskan spesifikasi yang salah atau alasan permintaan ini ditolak..."></textarea>
                            
                            @error('catatan_penolakan') 
                                <span class="text-red-600 text-xs mt-1 block font-medium">{{ $message }}</span> 
                            @enderror

                            <div class="flex justify-end space-x-2 mt-4">
                                <button wire:click="cancelReject" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-50">Batal</button>
                                <button wire:click="tolak" class="bg-red-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-red-700 shadow-sm">Konfirmasi Tolak</button>
                            </div>
                        </div>
                    @else
                        {{-- Tombol Aksi Default --}}
                        <div class="flex justify-end space-x-2">
                            <button wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded shadow-sm text-sm">Tutup</button>
                            
                            @if($permintaanDipilih->status_permintaan == 'Menunggu Persetujuan')
                                <button wire:click="confirmReject" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded shadow-sm text-sm font-semibold">Tolak Permintaan</button>
                                <button wire:click="approve('{{ $permintaanDipilih->id_permintaan }}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium">Setujui Permintaan</button>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif
</div>