<div wire:poll.3s>
    {{-- HEADER & DROPDOWN FILTER --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ringkasan Aktivitas Proyek Anda</h1>
            <p class="text-gray-500 text-sm">Pilih proyek di samping untuk memfilter data dashboard.</p>
        </div>
        
        <div class="w-full sm:w-72">
            <select wire:model.live="selectedProyek" class="w-full py-2.5 px-4 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white shadow-sm transition-all cursor-pointer">
                <option value="">-- Semua Proyek Penugasan --</option>
                @foreach($listPenugasan as $tugas)
                    <option value="{{ $tugas->id_proyek }}">
                        {{ $tugas->id_proyek }} - {{ $tugas->proyek->nama_proyek ?? 'Proyek Tidak Ada' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

  {{-- KARTU STATISTIK --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8"> {{-- Ubah dari md:grid-cols-2 ke md:grid-cols-3 --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-all hover:shadow-md">
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Total Permintaan</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalPermintaan }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-all hover:shadow-md">
        <div class="p-3 rounded-lg bg-blue-50 text-blue-600 mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Laporan Terkirim</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalLaporan }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6 flex items-center transition-all hover:shadow-md group">
        <div class="p-3 rounded-lg bg-red-50 text-red-600 mr-4 group-hover:bg-red-600 group-hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Tugas Belum Lapor</p>
            <h3 class="text-2xl font-bold {{ $tugasBelumSelesai > 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ $tugasBelumSelesai }}
            </h3>
        </div>
    </div>

</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- GRAFIK TREN PENGGUNAAN MATERIAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-1 flex flex-col">
            <h2 class="text-lg font-bold text-gray-800 mb-1">Tren Material</h2>
            <p class="text-xs text-gray-500 mb-6 font-medium">Top 5 material terpasang</p>
            
            {{-- wire:ignore sangat penting agar canvas tidak dihapus Livewire saat re-render --}}
            <div class="relative h-64 w-full flex-grow flex items-center justify-center" wire:ignore>
                <canvas id="trenMaterialChart"></canvas>
            </div>

            @if(empty($chartData))
                <div class="mt-4 text-center text-xs text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-medium">
                    Belum ada data material dipasang pada proyek ini.
                </div>
            @endif
        </div>

        {{-- TABEL LAPORAN TERBARU --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Laporan Penggunaan Terakhir</h2>
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">ID Laporan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Proyek</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Area</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($laporanTerbaru as $laporan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-bold text-blue-600">#{{ $laporan->id_penggunaan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium line-clamp-1">{{ $laporan->proyek->nama_proyek ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $laporan->area_pekerjaan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 italic bg-gray-50/50">
                                Belum ada laporan penggunaan untuk proyek ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 text-right">
                <a href="{{ route('pelaksanaan.penggunaan') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider gap-1">
                    Lihat Laporan Detail
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
    
   {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Buat Fungsi Global untuk merender Chart
        // (Bisa diakses kapan saja dan mencegah duplikasi kode)
        window.renderTrenMaterialChart = function(labels, data) {
            const ctx = document.getElementById('trenMaterialChart');
            if (!ctx) return; // Jika canvas tidak ditemukan, hentikan

            // HANCURKAN instance chart lama jika sudah ada agar tidak bentrok
            if (Chart.getChart('trenMaterialChart')) {
                Chart.getChart('trenMaterialChart').destroy();
            }

            // RENDER chart baru
            if (data && data.length > 0) {
                new Chart(ctx, {
                    type: 'doughnut', 
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { 
                                position: 'bottom', 
                                labels: { boxWidth: 10, padding: 15, font: {size: 11} } 
                            }
                        }
                    }
                });
            }
        };

        // 2. JALANKAN SAAT HALAMAN DIBUKA ATAU BERPINDAH SPA
        document.addEventListener('livewire:navigated', () => {
            // Cek apakah elemen ada di halaman ini
            if (document.getElementById('trenMaterialChart')) {
                window.renderTrenMaterialChart(@json($chartLabels), @json($chartData));
            }
        });

        // 3. JALANKAN SAAT DROPDOWN DIUBAH (Hanya didaftarkan 1 kali agar tidak bocor memory)
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('chart-updated', (event) => {
                // Livewire 3 menyimpan data payload di index 0
                const labels = event[0].labels;
                const data = event[0].data;
                
                // Panggil fungsi render ulang
                window.renderTrenMaterialChart(labels, data);
            });
        });
    </script>