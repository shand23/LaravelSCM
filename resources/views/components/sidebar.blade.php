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

        @if(auth()->user()->role === 'Admin')
            
            {{-- LABEL GROUP: KONSTRUKSI --}}
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Konstruksi</p>
            </div>

            {{-- Data Proyek --}}
            <a href="{{ route('admin.proyek') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.proyek') 
                  ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.proyek') ? 'text-white' : 'text-gray-500 group-hover:text-blue-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium text-sm">Data Proyek</span>
            </a>

            {{-- Penugasan Tim --}}
            <a href="{{ route('admin.penugasan') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.penugasan') 
                  ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.penugasan') ? 'text-white' : 'text-gray-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium text-sm">Penugasan Tim</span>
            </a>

            {{-- LABEL GROUP: LOGISTIK --}}
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Logistik</p>
            </div>

            {{-- Supplier (BARU) --}}
            <a href="{{ route('admin.supplier') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.supplier') 
                  ? 'bg-teal-600 text-white shadow-lg shadow-teal-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.supplier') ? 'text-white' : 'text-gray-500 group-hover:text-teal-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span class="font-medium text-sm">Supplier</span>
            </a>

            {{-- Material --}}
            <a href="{{ route('admin.material') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.material') 
                  ? 'bg-teal-600 text-white shadow-lg shadow-teal-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.material') ? 'text-white' : 'text-gray-500 group-hover:text-teal-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <span class="font-medium text-sm">Material</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.kategori') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.kategori') 
                  ? 'bg-teal-600 text-white shadow-lg shadow-teal-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.kategori') ? 'text-white' : 'text-gray-500 group-hover:text-teal-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span class="font-medium text-sm">Kategori</span>
            </a>

            {{-- LABEL GROUP: ADMIN --}}
            <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">System</p>
            </div>
            
            {{-- User Management --}}
            <a href="{{ route('admin.users') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1
               {{ request()->routeIs('admin.users') 
                  ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 translate-x-1' 
                  : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-gray-500 group-hover:text-purple-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium text-sm">Kelola User</span>
            </a>

        @endif

        @if(auth()->user()->role === 'Tim Proyek')
             {{-- Menu khusus tim proyek jika ada --}}
             <div class="mt-6 mb-2 px-4">
                <p class="text-xs font-bold text-purple-500 uppercase tracking-wider">Tugasku</p>
            </div>
             <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1 text-gray-400 hover:bg-gray-800 hover:text-white">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium text-sm">Proyek Saya</span>
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