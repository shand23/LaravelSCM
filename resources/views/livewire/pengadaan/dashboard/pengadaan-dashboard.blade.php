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
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Statistik (6 Kolom) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        
        {{-- Card: Total Supplier --}}
        <a href="{{ route('pengadaan.supplier') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Supplier</h4>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-gray-800">{{ $totalSupplier }}</span>
        </a>

        {{-- Card: Pesanan Aktif --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Draft RFQ</h4>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-gray-800">{{ $totalPesanan }}</span>
        </div>

        {{-- Card: Kontrak Disepakati --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">PO Aktif</h4>
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-gray-800">{{ $totalKontrak }}</span>
        </div>

        {{-- Card: Menunggu Pengiriman --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kirim</h4>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-gray-800">{{ $totalPengiriman }}</span>
        </div>

        {{-- CARD BARU: BARANG RETUR / MASALAH --}}
        <a href="{{ route('pengadaan.pengiriman') }}" class="bg-red-50 p-5 rounded-xl shadow-sm border border-red-100 hover:bg-red-100 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-red-500 uppercase tracking-widest">Retur/Masalah</h4>
                <div class="p-2 bg-red-500 text-white rounded-lg animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-red-700">{{ $totalRetur }}</span>
            <p class="text-[10px] text-red-400 mt-1 font-bold">Perlu Koordinasi</p>
        </a>

        {{-- Card: Tagihan --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tagihan</h4>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM12 4v16m8-8H4"></path></svg>
                </div>
            </div>
            <span class="text-2xl font-black text-gray-800">{{ $totalInvoice }}</span>
        </div>

    </div>

    {{-- Grid Grafik/Chart (2 Kolom x 2 Baris) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Chart 1: Tren Harga Pembelian --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-4">Tren Harga Pembelian Material (Rp)</h4>
            <div class="relative h-64 w-full">
                <canvas id="hargaChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Point Utama SCM Health --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 via-amber-400 to-emerald-500"></div>
            <h4 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Titik Utama: Kesehatan SCM
            </h4>
            <p class="text-[11px] text-gray-500 mb-4">Proporsi kelancaran vs hambatan logistik.</p>
            <div class="relative h-56 w-full flex-grow">
                <canvas id="scmHealthChart"></canvas>
            </div>
        </div>

        {{-- Chart 3: Kinerja Waktu Pengiriman --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-4">Kinerja Waktu Pengiriman Supplier</h4>
            <div class="relative h-64 w-full">
                <canvas id="kinerjaChart"></canvas>
            </div>
        </div>

        {{-- Chart 4: RINGKASAN KEUANGAN PENGADAAN --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM12 4v16m8-8H4"></path></svg>
                    Beban Keuangan (Rupiah)
                </h4>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="keuanganChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Script untuk Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. INIT CHART TREN HARGA (LINE CHART)
            const dataHarga = @json($trenHargaMaterial);
            new Chart(document.getElementById('hargaChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dataHarga.labels,
                    datasets: [{
                        label: 'Rata-rata Harga (Rp)',
                        data: dataHarga.data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
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
                            ticks: { font: {size: 10} }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { font: {size: 10} }
                        }
                    }
                }
            });

            // 2. INIT CHART SCM HEALTH (DOUGHNUT)
            const kesehatanData = @json($kesehatanSCM);
            const backgroundColors = kesehatanData.labels.map(label => {
                const text = label.toLowerCase();
                if (text.includes('selesai') || text.includes('penuh')) return '#10b981'; // Emerald
                if (text.includes('perjalanan')) return '#3b82f6'; // Blue
                if (text.includes('pending')) return '#f59e0b'; // Amber
                if (text.includes('return') || text.includes('masalah')) return '#ef4444'; // Red
                return '#8b5cf6'; // Purple
            });

            new Chart(document.getElementById('scmHealthChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: kesehatanData.labels,
                    datasets: [{
                        data: kesehatanData.data,
                        backgroundColor: backgroundColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', 
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { usePointStyle: true, padding: 20, font: {size: 11} } 
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) { label += ': '; }
                                    label += context.raw + ' Pengiriman';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // 3. INIT CHART KINERJA SUPPLIER (BAR CHART)
            const dataKinerja = @json($kinerjaSupplier);
            new Chart(document.getElementById('kinerjaChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dataKinerja.labels,
                    datasets: [{
                        label: 'Waktu Pengiriman (Hari)',
                        data: dataKinerja.data,
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                        borderRadius: 6,
                        barThickness: 24
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
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

            // 4. INIT CHART KEUANGAN (BAR CHART RUPIAH)
            const keuanganData = @json($grafikKeuangan);
            new Chart(document.getElementById('keuanganChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: keuanganData.labels,
                    datasets: [{
                        label: 'Total Nilai (Rp)',
                        data: keuanganData.data,
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)', // Indigo (PO Aktif)
                            'rgba(239, 68, 68, 0.8)',  // Merah (Invoice Pending)
                            'rgba(16, 185, 129, 0.8)'  // Hijau (Invoice Lunas)
                        ],
                        borderRadius: 6,
                        barThickness: 40
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
                                    let value = context.raw;
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                font: {size: 10},
                                callback: function(value) {
                                    if(value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                                    if(value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                    return 'Rp ' + value;
                                }
                            },
                            grid: { borderDash: [4, 4], color: '#f3f4f6' }
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: {size: 11, weight: 'bold'} } 
                        }
                    }
                }
            });

        });
    </script>
</div>