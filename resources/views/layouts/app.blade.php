<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        
        <div class="flex h-screen overflow-hidden">
            
            <x-sidebar /> 

            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                
                {{-- HEADER & NOTIFIKASI DINAMIS BERDASARKAN ROLE --}}
                @auth
                    @if(auth()->user()->ROLE === 'Logistik')
                        <livewire:notification-logistik />

                    @elseif(auth()->user()->ROLE === 'Top Manajemen')
                        {{-- Komponen Top Manajemen --}}
                        <livewire:notification-manajer />

                    @elseif(auth()->user()->ROLE === 'Tim Pelaksanaan')
                        {{-- Komponen Tim Pelaksanaan --}}
                        <livewire:notification-pelaksanaan /> 

                    @elseif(auth()->user()->ROLE === 'Tim Pengadaan')
                        {{-- Komponen Tim Pengadaan --}}
                      <livewire:notification-pengadaan /> 
                        
                    @else
                        {{-- Default Header Untuk 'Admin' atau Role lain yang tidak butuh lonceng khusus --}}
                        <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-20">
                            <div class="font-semibold text-lg text-gray-700">
                                SCM Material Konstruksi
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500 uppercase">{{ auth()->user()->ROLE }}</div>
                                </div>
                                <div class="h-9 w-9 rounded-full bg-gray-800 flex items-center justify-center text-white font-bold shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                        </header>
                    @endif
                @endauth
                {{-- AKHIR HEADER DINAMIS --}}

                @if (isset($header))
                    <header class="bg-white shadow-sm z-10">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
        
    </body>
</html>