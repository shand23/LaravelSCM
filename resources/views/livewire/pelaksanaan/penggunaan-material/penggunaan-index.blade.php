<div wire:poll.10s>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Laporan Penggunaan Material</h1>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition-colors">
            + Buat Laporan
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
    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID Laporan atau Area Pekerjaan..." 
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border px-3 py-2">
        </div>
    </div>

    {{-- TABEL DATA UTAMA --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Laporan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area Pekerjaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Laporan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($listLaporan as $laporan)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            {{ $laporan->id_penggunaan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporan->proyek->nama_proyek ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporan->area_pekerjaan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <button wire:click="bukaDetail('{{ $laporan->id_penggunaan }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200 inline-block mx-1 transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center italic">
                            Belum ada data laporan penggunaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $listLaporan->links() }}
        </div>
    </div>

    {{-- MODAL CREATE LAPORAN --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Buat Laporan Penggunaan Baru</h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="simpanLaporan">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 border-b pb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Permintaan Material</label>
                            <select wire:model.live="id_permintaan_selected" class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Referensi --</option>
                                @foreach($daftarPermintaan as $req) 
                                    <option value="{{ $req->id_permintaan }}">{{ $req->id_permintaan }} - {{ $req->proyek->nama_proyek ?? '' }}</option> 
                                @endforeach
                            </select>
                            @error('id_permintaan_selected') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Laporan</label>
                            <input type="date" wire:model="tanggal_laporan" 
                                   class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('tanggal_laporan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Area Pekerjaan</label>
                            <input type="text" wire:model="area_pekerjaan" placeholder="Contoh: Lantai 1, Gedung A"
                                   class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('area_pekerjaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan Umum</label>
                            <input type="text" wire:model="keterangan_umum" placeholder="Opsional..."
                                   class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    @if($id_permintaan_selected && count($detailBarang) > 0)
                    <div class="mb-3">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Detail Material Terpakai</label>
                        @error('detailBarang.*.*') <span class="text-red-500 text-xs mt-1 block font-normal">{{ $message }}</span> @enderror
                    </div>

                    <div class="border rounded-lg overflow-x-auto shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Terkirim</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Rusak/Reject</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Sisa/Retur</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Terpasang (Riil)</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($detailBarang as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800">
                                        {{ $item['nama_material'] }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm font-bold text-gray-700 bg-gray-50">
                                        {{ $item['jumlah_terkirim'] }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" 
                                               wire:model.live="detailBarang.{{ $index }}.jumlah_rusak_lapangan" 
                                               min="0" oninput="this.value = Math.abs(this.value)"
                                               class="w-20 mx-auto block p-2 bg-white border border-gray-300 focus:ring-red-500 focus:border-red-500 rounded-lg text-center font-bold text-red-600 sm:text-sm shadow-sm">
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" 
                                               wire:model.live="detailBarang.{{ $index }}.jumlah_sisa_material" 
                                               min="0" oninput="this.value = Math.abs(this.value)"
                                               class="w-20 mx-auto block p-2 bg-white border border-gray-300 focus:ring-yellow-500 focus:border-yellow-500 rounded-lg text-center font-bold text-yellow-600 sm:text-sm shadow-sm">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-bold border border-blue-200 w-24">
                                            {{ $item['jumlah_terpasang_riil'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="text" wire:model="detailBarang.{{ $index }}.catatan_khusus" placeholder="Opsional..."
                                               class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-2 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @elseif($id_permintaan_selected)
                        <p class="text-center text-gray-500 italic p-4 bg-gray-50 rounded-lg border border-gray-200">Menarik data material...</p>
                    @endif

                    <div class="flex justify-end space-x-3 mt-8 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL --}}
    @if($isModalDetailOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Detail Laporan: <span class="text-blue-600">{{ $laporanTerpilih->id_penggunaan ?? '' }}</span>
                </h2>
                <button wire:click="tutupDetail" class="text-gray-500 hover:text-red-500 text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Proyek</p>
                        <p class="font-bold text-gray-800">{{ $laporanTerpilih->proyek->nama_proyek ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">ID Permintaan</p>
                        <p class="font-bold text-gray-800">{{ $laporanTerpilih->id_permintaan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Area Pekerjaan</p>
                        <p class="font-bold text-gray-800">{{ $laporanTerpilih->area_pekerjaan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Pelaksana</p>
                        <p class="font-bold text-gray-800">{{ $laporanTerpilih->pelaksana->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div class="col-span-full mt-2 pt-2 border-t border-gray-200">
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Keterangan Umum</p>
                        <p class="text-sm text-gray-800">{{ $laporanTerpilih->keterangan_umum ?: 'Tidak ada keterangan.' }}</p>
                    </div>
                </div>
                
                <h3 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Detail Material Terpakai</h3>
                <div class="border rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Terpasang (Riil)</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Rusak</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Sisa</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Catatan Khusus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($detailItems as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $detail->material->nama_material ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-blue-600 bg-blue-50">{{ $detail->jumlah_terpasang_riil ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-red-600">{{ $detail->jumlah_rusak_lapangan ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-yellow-600">{{ $detail->jumlah_sisa_material ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $detail->catatan_khusus ?: '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 italic">Tidak ada detail material.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-end mt-8">
                    <button wire:click="tutupDetail" class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors">Tutup Jendela</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>