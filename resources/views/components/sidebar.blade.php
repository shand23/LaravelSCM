<aside class="w-64 bg-gray-900 text-white min-h-screen flex flex-col shadow-2xl transition-all duration-300 font-sans z-10 relative">
    
    {{-- HEADER LOGO --}}
    <div class="p-6 border-b border-gray-800 bg-gray-900">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-purple-800 rounded-lg flex items-center justify-center shadow-lg shadow-purple-900/50">
                <span class="font-bold text-xl text-white">S</span>
            </div>
            <div>
                <h2 class="text-base font-bold leading-tight tracking-wide text-gray-100">PT. Swevel</h2>
                <p class="text-xs text-purple-400">Universal Media</p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION MENU --}}
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        
        {{-- DASHBOARD --}}
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
           {{ request()->routeIs('dashboard') 
              ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
              : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="font-medium text-sm">Dashboard</span>
        </a>

        {{-- ================= ADMIN MENU ================= --}}
        @if(auth()->user()->ROLE === 'Admin')
            
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">System</p>
            </div>
            
            <a href="{{ route('admin.users') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.users') 
                  ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium text-sm">Kelola User</span>
            </a>

        @endif

      {{-- ================= TOP MANAJEMEN MENU ================= --}}
        @if(auth()->user()->ROLE === 'Top Manajemen')
            
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Manajerial</p>
            </div>
            
            <a href="{{ route('manajemen.proyek') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('manajemen.proyek') 
                  ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('manajemen.proyek') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="font-medium text-sm">Kelola Proyek</span>
            </a>

            <a href="{{ route('manajemen.penugasan') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('manajemen.penugasan') 
                  ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('manajemen.penugasan') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span class="font-medium text-sm">Penugasan Tim</span>
            </a>

            {{-- --- MENU APPROVAL BARU --- --}}
            <a href="{{ route('manajemen.approval') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('manajemen.approval') 
                  ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('manajemen.approval') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">Approval Material</span>
            </a>

        @endif

       {{-- ================= TIM PELAKSANAAN MENU ================= --}}
        @if(auth()->user()->ROLE === 'Tim Pelaksanaan')
             
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pekerjaan</p>
            </div>
             
         {{-- Menu Proyek Saya (HALAMAN BARU) --}}
    <a href="{{ route('pelaksanaan.proyek-saya') }}" 
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1 {{ request()->routeIs('pelaksanaan.proyek-saya*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pelaksanaan.proyek-saya*') ? 'text-white' : 'text-gray-500 group-hover:text-blue-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <span class="font-medium text-sm">Proyek Saya</span>
    </a>

            {{-- Menu Permintaan Material --}}
            <a href="{{ route('pelaksanaan.permintaan') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1 {{ request()->routeIs('pelaksanaan.permintaan*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('pelaksanaan.permintaan*') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="font-medium text-sm">Permintaan Material</span>
            </a>

            {{-- Menu Laporan Penggunaan (HALAMAN BARU) --}}
            <a href="{{ route('pelaksanaan.penggunaan') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1 {{ request()->routeIs('pelaksanaan.penggunaan*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('pelaksanaan.penggunaan*') ? 'text-white' : 'text-gray-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="font-medium text-sm">Laporan Penggunaan</span>
            </a>

        @endif
      {{-- ================= LOGISTIK MENU ================= --}}
            @if(auth()->user()->ROLE === 'Logistik')
                
                <div class="mt-6 mb-2 px-4">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Logistik & Material</p>
                </div>

                <a href="{{ route('logistik.kategori') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.kategori') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.kategori') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="font-medium text-sm">Kategori Material</span>
                </a>

                <a href="{{ route('logistik.material') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.material') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.material') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="font-medium text-sm">Data Material</span>
                </a>

                {{-- --- TRANSAKSI BARANG --- --}}
                <div class="mt-4 mb-2 px-4">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Transaksi Barang</p>
                </div>

                <a href="{{ route('logistik.pengajuan') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.pengajuan') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.pengajuan') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="font-medium text-sm">Pengajuan PR</span>
                </a>

                {{-- --- MANAJEMEN GUDANG --- --}}
                <div class="mt-4 mb-2 px-4">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Manajemen Gudang</p>
                </div>

                <a href="{{ route('logistik.penerimaan') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.penerimaan') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.penerimaan') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="font-medium text-sm">Penerimaan Gudang</span>
                </a>

                <a href="{{ route('logistik.stok') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.stok') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.stok') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    <span class="font-medium text-sm">Monitor Stok</span>
                </a>

                {{-- --- MENU BARU: PERMINTAAN PROYEK --- --}}
                <a href="{{ route('logistik.permintaan-proyek') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
                   {{ request()->routeIs('logistik.permintaan-proyek') 
                      ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('logistik.permintaan-proyek') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    <span class="font-medium text-sm">Permintaan Proyek</span>
                </a>

            @endif      
       {{-- ================= TIM PENGADAAN MENU ================= --}}
@if(auth()->user()->ROLE === 'Tim Pengadaan')
    
    <div class="mt-6 mb-2 px-4">
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pengadaan & Pembelian</p>
    </div>

    {{-- Menu Data Supplier --}}
    <a href="{{ route('pengadaan.supplier') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
       {{ request()->routeIs('pengadaan.supplier') 
          ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
          : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pengadaan.supplier') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <span class="font-medium text-sm">Data Supplier</span>
    </a>

    {{-- Menu Pesanan (RFQ) --}}
    <a href="{{ route('pengadaan.pesanan') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
       {{ request()->routeIs('pengadaan.pesanan') 
          ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
          : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pengadaan.pesanan') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="font-medium text-sm">Pesanan (RFQ)</span>
    </a>

    {{-- Menu Kontrak & PO --}}
    <a href="{{ route('pengadaan.kontrak') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
       {{ request()->routeIs('pengadaan.kontrak') 
          ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
          : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pengadaan.kontrak') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        <span class="font-medium text-sm">Kontrak & PO</span>
    </a>

    {{-- Menu Pengiriman Material (TAMBAHAN BARU) --}}
    <a href="{{ route('pengadaan.pengiriman') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
       {{ request()->routeIs('pengadaan.pengiriman') 
          ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
          : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('pengadaan.pengiriman') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span class="font-medium text-sm">Pengiriman Ekspedisi</span>
    </a>

@endif

    </nav>

    {{-- LOGOUT --}}
    <div class="p-4 border-t border-gray-800 bg-gray-900">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="font-medium text-sm">Logout</span>
                </div>
            </button>
        </form>
    </div>
</aside>