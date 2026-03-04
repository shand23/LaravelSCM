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

        {{-- ================= TIM PROYEK MENU ================= --}}
        @if(auth()->user()->ROLE === 'Tim Proyek')
             
             {{-- LABEL GROUP: TUGASKU --}}
             <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pekerjaan</p>
            </div>
             
             {{-- Proyek Saya --}}
             <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group mb-1 text-gray-400 hover:bg-gray-800 hover:text-white">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium text-sm">Proyek Saya</span>
            </a>

            {{-- LABEL GROUP: LOGISTIK --}}
             <div class="mt-6 mb-2 px-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Logistik</p>
            </div>

           

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