<div wire:poll.5s class="p-6 bg-gray-50 min-h-screen relative">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Logistik: Laporan & Analitik Penyesuaian</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Pantau tren kerusakan, kehilangan, dan evaluasi stok</p>
        </div>
        <a href="{{ route('logistik.stok') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Stok
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- WIDGET GRAFIK ANALITIK --}}
    {{-- ========================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Card 1: Grafik Donat (Komposisi Jenis) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-gray-700 tracking-wide">Distribusi Kendala</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4">Total Kasus Tercatat</p>
            </div>
            <div class="relative h-48 w-full flex items-center justify-center">
                @if(empty($grafikJenis))
                    <p class="text-xs text-gray-400 font-bold italic">Belum ada data visual</p>
                @else
                    <canvas id="chartJenis"></canvas>
                @endif
            </div>
        </div>

        {{-- Card 2: Grafik Garis (Tren Laporan) --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 md:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-gray-700 tracking-wide">Tren Laporan Masalah Stok</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4">30 Hari Terakhir</p>
            </div>
            <div class="relative h-48 w-full">
                @if(empty($grafikTren))
                    <div class="w-full h-full flex items-center justify-center">
                        <p class="text-xs text-gray-400 font-bold italic">Belum ada data transaksi dalam 30 hari terakhir</p>
                    </div>
                @else
                    <canvas id="chartTren"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TABEL DATA RIWAYAT --}}
    {{-- ========================================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b bg-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Material, Transaksi, atau Batch..." class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50">
            </div>

            <div class="w-full md:w-64">
                <select wire:model.live="filterJenis" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 py-2.5">
                    <option value="">Semua Jenis (Filter)</option>
                    <option value="Rusak">Barang Rusak</option>
                    <option value="Hilang">Barang Hilang</option>
                    <option value="Kadaluarsa">Barang Kadaluarsa</option>
                    <option value="Selisih Opname">Selisih Opname</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-[10px] font-black text-gray-300 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">Waktu & Transaksi</th>
                        <th class="px-6 py-4">Material & Batch</th>
                        <th class="px-6 py-4 text-center">Jenis</th>
                        <th class="px-6 py-4 text-center">Jumlah (- minus)</th>
                        <th class="px-6 py-4">Keterangan Laporan</th>
                        <th class="px-6 py-4 text-center rounded-tr-lg">Bukti Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($riwayatPenyesuaian as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</div>
                            <div class="text-[10px] text-gray-500 font-bold mt-1 bg-gray-100 inline-block px-2 py-0.5 rounded border border-gray-200">
                                {{ $item->id_penyesuaian }}
                            </div>
                            <div class="text-[10px] text-indigo-500 font-bold mt-1">
                                By: {{ $item->user->name ?? $item->id_user }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 uppercase">{{ $item->material->nama_material ?? 'Material Tidak Ditemukan' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 font-semibold">Batch: {{ $item->id_stok }}</div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->jenis_penyesuaian == 'Rusak')
                                <span class="bg-red-50 text-red-700 px-3 py-1 rounded-lg text-[11px] font-black border border-red-200">RUSAK</span>
                            @elseif($item->jenis_penyesuaian == 'Hilang')
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-[11px] font-black border border-gray-300">HILANG</span>
                            @elseif($item->jenis_penyesuaian == 'Kadaluarsa')
                                <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-lg text-[11px] font-black border border-amber-200">KADALUARSA</span>
                            @else
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-[11px] font-black border border-blue-200">OPNAME</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="text-lg font-black text-red-600">-{{ $item->jumlah_penyesuaian }}</span>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-600 font-medium line-clamp-2 max-w-xs" title="{{ $item->keterangan }}">
                                "{{ $item->keterangan }}"
                            </p>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->bukti_foto)
                                <button wire:click="lihatFoto('{{ $item->bukti_foto }}', '{{ addslashes($item->keterangan) }}', '{{ addslashes($item->material->nama_material ?? '') }}')" class="inline-flex items-center justify-center bg-indigo-50 hover:bg-indigo-500 text-indigo-600 hover:text-white p-2 rounded-lg transition-colors border border-indigo-100 group" title="Lihat Foto Bukti">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </button>
                            @else
                                <span class="text-[10px] text-gray-400 font-bold italic">Tanpa Foto</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-bold">Belum ada riwayat penyesuaian stok.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t bg-gray-50 rounded-b-2xl">
            {{ $riwayatPenyesuaian->links() }}
        </div>
    </div>

    {{-- MODAL PREVIEW FOTO --}}
    @if($isModalFotoOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden animate-[fadeIn_0.1s_ease-in]">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-800">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Bukti Penyesuaian: {{ $detailMaterial }}
                </h3>
                <button wire:click="closeModalFoto" class="text-gray-400 hover:text-white bg-gray-800 p-1.5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-1 bg-gray-100 relative group">
                <img src="{{ $fotoUrl }}" alt="Bukti Foto" class="w-full max-h-[60vh] object-contain rounded-lg">
            </div>
            
            <div class="px-6 py-4 bg-white border-t border-gray-100">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Catatan Pelapor:</p>
                <p class="text-sm text-gray-800 font-medium italic">"{{ $detailKeterangan }}"</p>
            </div>
        </div>
    </div>
    @endif

    {{-- SCRIPT INISIALISASI CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const dataJenis = @json($grafikJenis);
            const dataTren = @json($grafikTren);

            // 1. Inisialisasi Chart Donat (Jenis Kendala)
            if(Object.keys(dataJenis).length > 0) {
                const ctxJenis = document.getElementById('chartJenis');
                
                // Pemetaan warna agar sesuai dengan warna badge UI
                const warnaMap = {
                    'Rusak': '#ef4444',         // Merah
                    'Hilang': '#6b7280',        // Abu-abu
                    'Kadaluarsa': '#f59e0b',    // Kuning Amber
                    'Selisih Opname': '#3b82f6' // Biru
                };

                const warnaBackground = Object.keys(dataJenis).map(jenis => warnaMap[jenis] || '#10b981');

                new Chart(ctxJenis, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(dataJenis),
                        datasets: [{
                            data: Object.values(dataJenis),
                            backgroundColor: warnaBackground,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10, family: 'sans-serif' } } }
                        },
                        cutout: '70%'
                    }
                });
            }

            // 2. Inisialisasi Chart Garis (Tren)
            if(Object.keys(dataTren).length > 0) {
                const ctxTren = document.getElementById('chartTren');
                new Chart(ctxTren, {
                    type: 'line',
                    data: {
                        labels: Object.keys(dataTren), // Menampilkan Tanggal
                        datasets: [{
                            label: 'Jumlah Kasus Masuk',
                            data: Object.values(dataTren),
                            borderColor: '#6366f1', // Warna Indigo Tailwind
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3, // Efek curve melengkung
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#6366f1',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { borderDash: [4, 4], color: '#f3f4f6' } },
                            x: { ticks: { font: { size: 10 }, maxTicksLimit: 7 }, grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
</div>