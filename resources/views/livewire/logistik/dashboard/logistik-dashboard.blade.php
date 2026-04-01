<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Logistik & Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Sambutan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl mb-6 border border-gray-100">
                <div class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-extrabold text-green-700">Halo, Tim Logistik! 📦</h3>
                        <p class="text-sm text-gray-500 mt-1">Pantau ketersediaan material, alur masuk-keluar barang, dan pemenuhan permintaan proyek hari ini.</p>
                    </div>
                    <div>
                        <span class="bg-green-50 text-green-600 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-green-100 shadow-sm">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Grid Kartu Statistik --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                {{-- Card: Master Material --}}
                <a href="{{ route('logistik.material') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all group col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-gray-700">Total Material</h4>
                        <div class="p-1.5 bg-gray-50 text-gray-600 rounded-lg group-hover:bg-gray-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800">{{ $totalMaterial }}</h2>
                </a>

                {{-- Card: Pengajuan (PR) --}}
                <a href="{{ route('logistik.pengajuan') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 transition-all group col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-blue-600">Pengajuan Beli (PR)</h4>
                        <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800">{{ $totalPengajuan }}</h2>
                </a>

                {{-- Card: Penerimaan Pending --}}
                <a href="{{ route('logistik.penerimaan') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-300 transition-all group col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-emerald-600">Menunggu Datang</h4>
                        <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800">{{ $totalPenerimaanPending }}</h2>
                </a>

                {{-- Card: Permintaan Proyek --}}
                <a href="{{ route('logistik.permintaan-proyek') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-300 transition-all group col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-amber-600">Permintaan Proyek</h4>
                        <div class="p-1.5 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800">{{ $totalPermintaanPending }}</h2>
                </a>

                {{-- Card: Penyesuaian Stok --}}
                <a href="{{ route('logistik.penyesuaian') }}" class="bg-red-50 p-5 rounded-xl shadow-sm border border-red-200 hover:shadow-md hover:border-red-400 transition-all group col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[10px] font-bold text-red-700 uppercase tracking-wider">Kasus Penyesuaian</h4>
                        <div class="p-1.5 bg-red-100 text-red-600 rounded-lg group-hover:bg-red-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-red-700">{{ $totalPenyesuaian }}</h2>
                </a>
            </div>

            {{-- ========================================== --}}
            {{-- WIDGET ANALITIK LOGISTIK --}}
            {{-- ========================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Widget 1: Alur Barang (Area Chart) - Lebih Lebar (Col-Span-2) --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-sm font-extrabold text-gray-700">Volume Alur Barang (Masuk vs Keluar)</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pergerakan Stok Gudang 6 Bulan Terakhir</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="flex items-center text-[10px] font-bold text-gray-500"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> Masuk</span>
                            <span class="flex items-center text-[10px] font-bold text-gray-500"><span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span> Keluar</span>
                        </div>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="chartAlurBarang"></canvas>
                    </div>
                </div>

                {{-- Widget 2: Tren Penyesuaian (Bar Chart) --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-sm font-extrabold text-gray-700">Tren Penyesuaian Stok</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Frekuensi Kasus per Bulan</p>
                        </div>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="chartTrenPenyesuaian"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            
            const dataAlur = @json($alurBarang);
            const dataPenyesuaian = @json($trenPenyesuaian);

            // ==========================================
            // WIDGET 1: ALUR BARANG MASUK VS KELUAR (AREA CHART)
            // ==========================================
            const ctxAlur = document.getElementById('chartAlurBarang').getContext('2d');
            
            // Gradasi Masuk (Hijau)
            let gradMasuk = ctxAlur.createLinearGradient(0, 0, 0, 300);
            gradMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); 
            gradMasuk.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            // Gradasi Keluar (Oranye)
            let gradKeluar = ctxAlur.createLinearGradient(0, 0, 0, 300);
            gradKeluar.addColorStop(0, 'rgba(249, 115, 22, 0.4)'); 
            gradKeluar.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

            new Chart(ctxAlur, {
                type: 'line',
                data: {
                    labels: dataAlur.labels,
                    datasets: [
                        {
                            label: 'Volume Masuk (Penerimaan)',
                            data: dataAlur.masuk,
                            borderColor: '#10b981', // Emerald 500
                            backgroundColor: gradMasuk,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4 // Smooth curve
                        },
                        {
                            label: 'Volume Keluar (Pengeluaran)',
                            data: dataAlur.keluar,
                            borderColor: '#f97316', // Orange 500
                            backgroundColor: gradKeluar,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }, // Legend dimatikan karena sudah dibuat kustom di HTML
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { font: {size: 10} }
                        },
                        x: { grid: { display: false }, ticks: { font: {size: 10} } }
                    }
                }
            });

            // ==========================================
            // WIDGET 2: TREN PENYESUAIAN STOK (BAR CHART)
            // ==========================================
            const ctxPenyesuaian = document.getElementById('chartTrenPenyesuaian').getContext('2d');
            
            new Chart(ctxPenyesuaian, {
                type: 'bar',
                data: {
                    labels: dataPenyesuaian.labels,
                    datasets: [{
                        label: 'Frekuensi Penyesuaian',
                        data: dataPenyesuaian.data,
                        backgroundColor: '#ef4444', // Red 500
                        borderRadius: 4,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { stepSize: 1, font: {size: 10} }
                        },
                        x: { grid: { display: false }, ticks: { font: {size: 10} } }
                    }
                }
            });

        });
    </script>
</div>