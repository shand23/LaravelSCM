<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ringkasan Manajemen Proyek</h1>
        <p class="text-gray-500 text-sm">Pantau status proyek, penugasan tim, dan permintaan material yang membutuhkan persetujuan.</p>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Card Proyek Aktif --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Proyek Aktif</p>
                <p class="text-2xl font-black text-gray-800">{{ $proyekAktif }} <span class="text-sm font-medium text-gray-400">/ {{ $totalProyek }}</span></p>
            </div>
        </div>

        {{-- Card Tim Ditugaskan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Penugasan Aktif</p>
                <p class="text-2xl font-black text-gray-800">{{ $timAktif }}</p>
            </div>
        </div>

        {{-- Card Menunggu Approval --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center lg:col-span-2">
            <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Permintaan Perlu Persetujuan</p>
                    <p class="text-2xl font-black text-gray-800">{{ $menungguApproval }}</p>
                </div>
                @if($menungguApproval > 0)
                    <a href="{{ route('manajemen.approval') }}" class="px-4 py-2 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg border border-amber-200 hover:bg-amber-100 transition">
                        Review Sekarang &rarr;
                    </a>
                @endif
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- TABEL PROYEK TERBARU --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Proyek Terbaru</h2>
                <a href="{{ route('manajemen.proyek') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Nama Proyek</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Mulai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($proyekTerbaru as $proyek)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $proyek->nama_proyek }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full 
                                    {{ $proyek->status_proyek === 'Aktif' ? 'bg-blue-100 text-blue-700' : ($proyek->status_proyek === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $proyek->status_proyek }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 italic">Belum ada proyek.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL PERMINTAAN MENUNGGU APPROVAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Permintaan Material (Pending)</h2>
                <a href="{{ route('manajemen.approval') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Ke Approval</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Proyek</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($permintaanPending as $permintaan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-bold text-amber-600">#{{ $permintaan->id_permintaan }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 line-clamp-1" title="{{ $permintaan->proyek->nama_proyek ?? '-' }}">
                                {{ $permintaan->proyek->nama_proyek ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 italic">Semua permintaan sudah direspons.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>