<div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-6 items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Monitoring Penggunaan Material</h1>
            <p class="text-sm text-gray-500 font-medium">Rekapitulasi seluruh penggunaan material proyek secara real-time.</p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="bg-white p-5 mb-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Cari Laporan</label>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="ID Laporan, Proyek, atau Area..." 
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div class="w-full md:w-64">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Filter Proyek</label>
            <select wire:model.live="filterProyek" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">Semua Proyek</option>
                @foreach($listProyek as $proyek)
                    <option value="{{ $proyek->id_proyek }}">{{ $proyek->nama_proyek }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tgl Laporan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Proyek / Pelaksana</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Area Pekerjaan</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($listLaporan as $laporan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                        {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $laporan->proyek->nama_proyek ?? '-' }}</div>
                        <div class="text-xs text-gray-500 font-medium">Oleh: {{ $laporan->pelaksana->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $laporan->area_pekerjaan }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button wire:click="bukaDetail('{{ $laporan->id_penggunaan }}')" 
                                class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider p-2 bg-blue-50 rounded-lg transition-colors">
                            Lihat Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $listLaporan->links() }}
        </div>
    </div>

    {{-- MODAL DETAIL (Sama seperti referensi Anda, hanya view) --}}
    @if($isModalDetailOpen && $laporanTerpilih)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Penggunaan Material</h3>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">{{ $laporanTerpilih->id_penggunaan }}</p>
                    </div>
                    <button wire:click="tutupDetail" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    {{-- Grid Informasi Utama --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Proyek</p>
                            <p class="text-sm font-bold text-gray-800 uppercase">{{ $laporanTerpilih->proyek->nama_proyek ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Area Pekerjaan</p>
                            <p class="text-sm font-bold text-gray-800">{{ $laporanTerpilih->area_pekerjaan }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Tanggal Laporan</p>
                            <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($laporanTerpilih->tanggal_laporan)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Pelaksana Lapangan</p>
                            <p class="text-sm font-bold text-gray-800">{{ $laporanTerpilih->pelaksana->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase italic">Riil Terpasang</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase italic text-red-600">Rusak</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase italic text-yellow-600">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($detailItems as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ $detail->material->nama_material ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-black text-blue-600 bg-blue-50/30">{{ $detail->jumlah_terpasang_riil }} {{ $detail->material->satuan }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-bold text-red-600">{{ $detail->jumlah_rusak_lapangan }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-bold text-yellow-600">{{ $detail->jumlah_sisa_material }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                    <button wire:click="tutupDetail" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-6 py-2 rounded-lg transition-colors">Tutup Jendela</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>