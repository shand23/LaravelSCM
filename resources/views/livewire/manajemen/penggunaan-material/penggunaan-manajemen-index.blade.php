<div wire:poll.10s>
    {{-- HEADER dengan tombol ekspor --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Monitoring Penggunaan Material</h1>
            <p class="text-sm text-gray-500">Rekapitulasi seluruh penggunaan material, analisis tren, dan ekspor data.</p>
        </div>
        <button wire:click="exportPdf" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
            Ekspor PDF
        </button>
    </div>

    {{-- FILTER PERIODE & PROYEK (baru) --}}
    <div class="bg-white p-5 mb-6 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-end">
        <div class="w-full md:w-64">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode</label>
            <select wire:model.live="periode" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                <option value="semua">Semua Periode</option>
                <option value="bulan_ini">Bulan Ini</option>
                <option value="3_bulan">3 Bulan Terakhir</option>
                <option value="6_bulan">6 Bulan Terakhir</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        @if($periode == 'custom')
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
            <input type="date" wire:model.live="customStart" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
            <input type="date" wire:model.live="customEnd" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
        </div>
        @endif

        <div class="w-full md:w-64">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Proyek</label>
            <select wire:model.live="filterProyek" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                <option value="">Semua Proyek</option>
                @foreach($listProyek as $proyek)
                    <option value="{{ $proyek->id_proyek }}">{{ $proyek->nama_proyek }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Laporan</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="ID Laporan, Proyek, atau Area..."
                   class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
        </div>
    </div>

    {{-- KARTU STATISTIK (style dashboard logistik) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Card: Total Terpasang --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 transition-all group">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-blue-600">Total Terpasang</h4>
            <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <h2 class="text-2xl font-black text-gray-800">{{ number_format($totalTerpasang, 0, ',', '.') }}</h2>
    </div>

    {{-- Card: Total Rusak --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-red-300 transition-all group">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-red-600">Total Rusak</h4>
            <div class="p-1.5 bg-red-50 text-red-600 rounded-lg group-hover:bg-red-600 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
        <h2 class="text-2xl font-black text-gray-800">{{ number_format($totalRusak, 0, ',', '.') }}</h2>
    </div>

    {{-- Card: Total Sisa --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-300 transition-all group">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-yellow-600">Total Sisa</h4>
            <div class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 4v16"/>
                </svg>
            </div>
        </div>
        <h2 class="text-2xl font-black text-gray-800">{{ number_format($totalSisa, 0, ',', '.') }}</h2>
    </div>

    {{-- Card: Proyek Aktif --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-300 transition-all group">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-emerald-600">Proyek Aktif</h4>
            <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        </div>
        <h2 class="text-2xl font-black text-gray-800">{{ $jumlahProyekAktif }}</h2>
    </div>
</div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100" wire:ignore>
            <h4 class="text-sm font-bold text-gray-700 mb-2">Tren Penggunaan Material (6 Bulan)</h4>
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-3">Terpasang vs Rusak vs Sisa</p>
            <canvas id="chartTrenPenggunaan" class="h-64 w-full"></canvas>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100" wire:ignore>
            <h4 class="text-sm font-bold text-gray-700 mb-2">Top 5 Kategori Material Terpasang</h4>
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-3">Berdasarkan volume penggunaan</p>
            <canvas id="chartKategoriPenggunaan" class="h-64 w-full"></canvas>
        </div>
    </div>

    {{-- TABEL LAPORAN --}}
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
                        <div class="text-xs text-gray-500 font-medium">Oleh: {{ $laporan->pelaksana->nama_lengkap ?? '-' }}</div>
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

    {{-- MODAL DETAIL (ASLI, tidak diubah) --}}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <div><span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Proyek</span><br>{{ $laporanTerpilih->proyek->nama_proyek ?? '-' }}</div>
                        <div><span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Area Pekerjaan</span><br>{{ $laporanTerpilih->area_pekerjaan }}</div>
                        <div><span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Tanggal Laporan</span><br>{{ \Carbon\Carbon::parse($laporanTerpilih->tanggal_laporan)->translatedFormat('d F Y') }}</div>
                        <div><span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Pelaksana</span><br>{{ $laporanTerpilih->pelaksana->nama_lengkap ?? '-' }}</div>
                    </div>
                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Riil Terpasang</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase text-red-600">Rusak</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase text-yellow-600">Sisa</th>
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
                    <button wire:click="tutupDetail" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-6 py-2 rounded-lg transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            const labelsTren = @json($labelsTren);
            const dataTerpasang = @json($dataTerpasang);
            const dataRusak = @json($dataRusak);
            const dataSisa = @json($dataSisa);
            const labelsKategori = @json($labelsKategori);
            const dataKategori = @json($dataKategori);

            if (Chart.getChart('chartTrenPenggunaan')) Chart.getChart('chartTrenPenggunaan').destroy();
            if (Chart.getChart('chartKategoriPenggunaan')) Chart.getChart('chartKategoriPenggunaan').destroy();

            if (labelsTren.length) {
                const ctxTren = document.getElementById('chartTrenPenggunaan').getContext('2d');
                new Chart(ctxTren, {
                    type: 'line',
                    data: {
                        labels: labelsTren,
                        datasets: [
                            { label: 'Terpasang', data: dataTerpasang, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3 },
                            { label: 'Rusak', data: dataRusak, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)', fill: true, tension: 0.3 },
                            { label: 'Sisa', data: dataSisa, borderColor: '#eab308', backgroundColor: 'rgba(234,179,8,0.05)', fill: true, tension: 0.3 }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
                });
            }
            if (labelsKategori.length) {
                const ctxKat = document.getElementById('chartKategoriPenggunaan').getContext('2d');
                new Chart(ctxKat, {
                    type: 'bar',
                    data: { labels: labelsKategori, datasets: [{ label: 'Jumlah Terpasang', data: dataKategori, backgroundColor: '#f59e0b', borderRadius: 6 }] },
                    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
                });
            }
        });
    </script>
</div>