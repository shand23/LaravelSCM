<header wire:poll.1s class="bg-white shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-20">

    <div class="font-semibold text-lg text-gray-700">
        SCM Material Konstruksi
    </div>

    <div class="flex items-center gap-6">
        
        {{-- CEK ROLE PENGADAAN: Ikon Lonceng Hanya Muncul Untuk Tim Pengadaan --}}
        @if(auth()->user()->ROLE == 'Tim Pengadaan')
            <div x-data="{ open: false }" 
                 @play-notif-sound.window="new Audio('{{ asset('notifikasi.mp3') }}').play().catch(() => {})"
                 class="relative">
                
                <button @click="open = !open" @click.outside="open = false" class="relative p-1 rounded-full hover:bg-gray-100 transition group focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    
                    @if($jmlNotifUnread > 0)
                        <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-[10px] text-white font-bold border-2 border-white">
                            {{ $jmlNotifUnread }}
                        </span>
                        <span class="absolute top-0 right-0 h-4 w-4 bg-red-400 rounded-full animate-ping opacity-75"></span>
                    @endif
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 transform"
                     x-transition:enter-end="opacity-100 scale-100 transform"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 transform"
                     x-transition:leave-end="opacity-0 scale-95 transform"
                     class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
                     style="display: none;">
                    
                    <div class="bg-gray-50/80 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800">Notifikasi Pengadaan</h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $jmlNotifUnread }} Baru</span>
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        @forelse($notifikasi as $notif)
                            <a wire:click.prevent="markAsRead('{{ $notif->notif_id }}', '{{ $notif->type }}')" href="#" 
                               class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition bg-white">
                                <div class="flex items-center justify-between">
                                    {{-- Label & Warna Dinamis --}}
                                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $notif->color }}">
                                        {{ $notif->label }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium">
                                        {{ \Carbon\Carbon::parse($notif->updated_at)->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-800 mt-1 font-semibold">
                                    {{ $notif->desc }}
                                </p>

                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    @if($notif->type == 'pengiriman')
                                        <span class="text-indigo-500">●</span> Pantau status pengiriman / return dari supplier.
                                    @else
                                        <span class="text-green-500">●</span> Logistik mengajukan PR, segera proses RFQ.
                                    @endif
                                </p>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-sm text-gray-500 text-center">
                                Semua tugas selesai. Tidak ada notifikasi baru.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif
        {{-- AKHIR CEK ROLE PENGADAAN --}}

        {{-- Profil User (Tetap Tampil Untuk Semua Role) --}}
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-medium text-gray-900">
                    {{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'Pengguna' }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ auth()->user()->ROLE ?? 'Staff' }}
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