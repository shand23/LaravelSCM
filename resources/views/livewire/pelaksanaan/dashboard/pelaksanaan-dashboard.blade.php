<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ringkasan Aktivitas Proyek Anda</h1>
        <p class="text-gray-500 text-sm">Berikut adalah data berdasarkan proyek yang ditugaskan kepada Anda.</p>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Permintaan Proyek</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPermintaan }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Laporan Penggunaan Material</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalLaporan }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- GRAFIK TREN PENGGUNAAN MATERIAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Tren Material</h2>
            <p class="text-xs text-gray-500 mb-4">Top 5 material historis dipasang</p>
            
            <div class="relative h-64 w-full">
                <canvas id="trenMaterialChart"></canvas>
            </div>

            @if(empty($chartData))
                <div class="mt-4 text-center text-sm text-gray-500 italic bg-gray-50 p-3 rounded">
                    Belum ada data material dipasang.
                </div>
            @endif
        </div>

        {{-- TABEL LAPORAN TERBARU --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Laporan Terakhir Anda</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($laporanTerbaru as $laporan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-blue-600">{{ $laporan->id_penggunaan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $laporan->proyek->nama_proyek ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $laporan->area_pekerjaan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500 italic">Belum ada laporan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 text-right">
                <a href="{{ route('pelaksanaan.penggunaan') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Lihat Laporan Detail &rarr;
                </a>
            </div>
        </div>
    </div>
    
    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('trenMaterialChart');
            const labels = @json($chartLabels);
            const data = @json($chartData);

            if (ctx && data.length > 0) {
                new Chart(ctx, {
                    type: 'doughnut', 
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                            borderWidth: 1
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12 } }
                        }
                    }
                });
            }
        });
    </script>
</div>