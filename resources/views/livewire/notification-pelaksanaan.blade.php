<header wire:poll.3s class="bg-white shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-20">

    <div class="font-semibold text-lg text-gray-700">
        SCM - Panel Pelaksanaan
    </div>

    <div class="flex items-center gap-6">
        
        @if(auth()->user()->ROLE == 'Tim Pelaksanaan')
            <div x-data="{ open: false }" 
                 @play-notif-sound.window="new Audio('{{ asset('ashiap.mp3') }}').play().catch(() => {})"
                 class="relative">
                
                <button @click="open = !open" @click.outside="open = false" class="relative p-1 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    
                    @if($jmlNotifUnread > 0)
                        <span class="absolute top-0 right-0 h-4 w-4 bg-red-600 rounded-full flex items-center justify-center text-[10px] text-white font-bold border-2 border-white">
                            {{ $jmlNotifUnread }}
                        </span>
                    @endif
                </button>

                <div x-show="open" 
                     x-transition class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
                     style="display: none;">
                    
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800">Pemberitahuan Proyek</h3>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $jmlNotifUnread }} Update</span>
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        @forelse($notifikasi as $notif)
                            <a wire:click.prevent="markAsRead('{{ $notif->notif_id }}')" href="#" 
                               class="block px-4 py-4 border-b hover:bg-blue-50 transition bg-white">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $notif->color }}">
                                        {{ $notif->label }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        {{ \Carbon\Carbon::parse($notif->updated_at)->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">{{ $notif->desc }}</p>
                                <p class="text-[11px] text-gray-500 mt-1">Klik untuk detail permintaan proyek.</p>
                            </a>
                        @empty
                            <div class="px-4 py-10 text-center text-gray-500">
                                <p class="text-sm">Tidak ada pembaruan status saat ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-bold text-gray-900">{{ auth()->user()->nama_lengkap }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-tighter">{{ auth()->user()->ROLE }}</div>
            </div>
            <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}
            </div>
        </div>
    </div>
</header>