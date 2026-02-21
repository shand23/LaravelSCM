<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Administrator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-indigo-600">Selamat Datang, Admin!</h3>
                    <p class="mb-4">Anda memiliki akses penuh ke sistem.</p>
                    
                    <a href="{{ route('admin.users') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Kelola User &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>