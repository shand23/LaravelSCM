<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengadaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Memanggil Komponen Livewire Dashboard Pengadaan --}}
            @livewire('pengadaan.dashboard.pengadaan-dashboard')
        </div>
    </div>
</x-app-layout>