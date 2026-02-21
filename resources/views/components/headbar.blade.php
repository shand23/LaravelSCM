<header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-20">

    <div class="font-semibold text-lg text-gray-700">
        SCM Material Konstruksi
    </div>

    <div class="flex items-center gap-6">
    {{-- Notifikasi --}}
        <button class="relative p-1 rounded-full hover:bg-gray-100 transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            
            {{-- Badge merah akan muncul jika ada proyek yang lewat tenggat --}}
            @if($jmlNotif > 0)
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full border-2 border-white transform translate-x-1/4 -translate-y-1/4">
                    {{ $jmlNotif }}
                </span>
            @endif
        </button>

        {{-- Profil User --}}
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-medium text-gray-900">
                    {{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'Pengguna' }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ auth()->user()->role ?? 'Staff' }}
                </div>
            </div>

            @php
                $name = auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'U';
                $initial = strtoupper(substr($name, 0, 1));
            @endphp
            <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                {{ $initial }}
            </div>
        </div>

    </div>
</header>