<div>
    <div class="mb-4">
        <h1 class="text-xl font-bold text-gray-800">Persetujuan Pengajuan Material (PM)</h1>
        <p class="text-sm text-gray-500">Daftar pengajuan dari tim pelaksanaan yang memerlukan verifikasi Anda.</p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 border border-green-400">{{ session('message') }}</div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengaju</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($approvals as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-bold">{{ $item->id_pengajuan }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item->proyek->nama_proyek }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item->userPengaju->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tanggal_pengajuan }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button wire:click="showDetail('{{ $item->id_pengajuan }}')" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded">
                                Periksa & ACC
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pengajuan yang menunggu persetujuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $approvals->links() }}</div>
    </div>

    @if($isModalDetailOpen && $selectedPengajuan)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                <h3 class="text-lg font-bold">Detail Pengajuan: {{ $selectedPengajuan->id_pengajuan }}</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm"><strong>Proyek:</strong> {{ $selectedPengajuan->proyek->nama_proyek }}</p>
                    <p class="text-sm"><strong>Catatan:</strong> {{ $selectedPengajuan->catatan_pengajuan ?: '-' }}</p>
                </div>

                <table class="min-w-full border mb-6">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left text-xs">Material</th>
                            <th class="border px-3 py-2 text-center text-xs">Jumlah Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedPengajuan->detailPengajuan as $detail)
                        <tr>
                            <td class="border px-3 py-2 text-sm">{{ $detail->material->nama_material }}</td>
                            <td class="border px-3 py-2 text-center text-sm font-bold">{{ $detail->jumlah_diajukan }} {{ $detail->material->satuan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Tutup</button>
                    <button wire:click="reject('{{ $selectedPengajuan->id_pengajuan }}')" wire:confirm="Tolak pengajuan ini?" class="px-4 py-2 bg-red-600 text-white rounded">Tolak</button>
                    <button wire:click="approve('{{ $selectedPengajuan->id_pengajuan }}')" wire:confirm="Setujui pengajuan ini?" class="px-4 py-2 bg-green-600 text-white rounded">Setujui (ACC)</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>