<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Tim Pelaksanaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Menyisipkan komponen Livewire --}}
            @livewire('pelaksanaan.dashboard.pelaksanaan-dashboard')
        </div>
    </div>
</x-app-layout>