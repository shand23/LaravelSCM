<div>
    {{-- Header Sambutan --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
        <div class="p-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-extrabold text-indigo-700">Halo, Tim Pengadaan! 👋</h3>
                <p class="text-sm text-gray-500 mt-1">Pantau progres pesanan, analitik rantai pasok, dan kinerja supplier Anda hari ini.</p>
            </div>
            <div class="hidden md:block">
                <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-indigo-100">
                    {{ now()->format('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Card: Total Supplier --}}
        <a href="{{ route('pengadaan.supplier') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-blue-600">Supplier Aktif</h4>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-gray-800">{{ $totalSupplier }}</h2>
        </a>

        {{-- Card: Pesanan (PO/RFQ) --}}
        <a href="{{ route('pengadaan.pesanan') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-amber-600">Pesanan (RFQ)</h4>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-gray-800">{{ $totalPesanan }}</h2>
        </a>

        {{-- Card: Kontrak --}}
        <a href="{{ route('pengadaan.kontrak') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-emerald-600">Kontrak & PO</h4>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-gray-800">{{ $totalKontrak }}</h2>
        </a>

        {{-- Card: Pengiriman --}}
        <a href="{{ route('pengadaan.pengiriman') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-purple-600">Pengiriman</h4>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-gray-800">{{ $totalPengiriman }}</h2>
        </a>

        {{-- Card: Invoice --}}
        <a href="{{ route('pengadaan.invoice') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-rose-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-rose-600">Invoice</h4>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-gray-800">{{ $totalInvoice }}</h2>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- WIDGET ANALISIS RANTAI PASOK --}}
    {{-- ========================================== --}}
    <div class="mb-4">
        <h3 class="text-lg font-extrabold text-gray-800">Analisis Rantai Pasok & Kinerja</h3>
        <p class="text-xs text-gray-500">Pantau fluktuasi harga material dan evaluasi kecepatan pengiriman vendor.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Widget 1: Tren Harga Material (Line Chart) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h4 class="text-sm font-extrabold text-gray-700">Tren Harga Material Utama</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Semen Portland 50Kg (6 Bulan Terakhir)</p>
                </div>
                <span class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-1 rounded font-bold">Naik 3.8%</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="chartTrenHarga"></canvas>
            </div>
        </div>

        {{-- Widget 2: Kinerja Supplier (Bar Chart) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h4 class="text-sm font-extrabold text-gray-700">Evaluasi Kinerja Waktu Pengiriman</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Lead Time Rata-rata (Hari)</p>
                </div>
                <span class="bg-emerald-50 text-emerald-600 text-[10px] px-2 py-1 rounded font-bold">Makin Rendah Makin Baik</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="chartKinerjaSupplier"></canvas>
            </div>
        </div>

    </div>

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // 1. Data dari komponen PHP (Livewire)
            const dataHarga = @json($trenHargaMaterial);
            const dataKinerja = @json($kinerjaSupplier);

            // ==========================================
            // WIDGET 1: TREN HARGA MATERIAL (LINE CHART)
            // ==========================================
            const ctxHarga = document.getElementById('chartTrenHarga').getContext('2d');
            
            // Membuat gradient untuk Line Chart
            let gradientHarga = ctxHarga.createLinearGradient(0, 0, 0, 400);
            gradientHarga.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); // Indigo
            gradientHarga.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(ctxHarga, {
                type: 'line',
                data: {
                    labels: dataHarga.labels,
                    datasets: [{
                        label: 'Harga (Rp)',
                        data: dataHarga.data,
                        borderColor: '#4f46e5', // Indigo-600
                        backgroundColor: gradientHarga,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung (smooth)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: false,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { font: {size: 10} }
                        },
                        x: { grid: { display: false }, ticks: { font: {size: 10} } }
                    }
                }
            });

            // ==========================================
            // WIDGET 2: KINERJA SUPPLIER (BAR CHART)
            // ==========================================
            const ctxKinerja = document.getElementById('chartKinerjaSupplier').getContext('2d');
            
            new Chart(ctxKinerja, {
                type: 'bar', // Menggunakan diagram batang
                data: {
                    labels: dataKinerja.labels,
                    datasets: [{
                        label: 'Rata-rata Waktu Pengiriman (Hari)',
                        data: dataKinerja.data,
                        backgroundColor: [
                            '#10b981', // Emerald (Bagus / Cepat)
                            '#f59e0b', // Amber
                            '#3b82f6', // Blue
                            '#ef4444', // Red (Buruk / Lambat)
                            '#8b5cf6'  // Purple
                        ],
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 24
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Hari', font: {size: 10} },
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { stepSize: 1, font: {size: 10} }
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: {size: 10}, maxRotation: 45, minRotation: 45 } 
                        }
                    }
                }
            });

        });
    </script>
</div>