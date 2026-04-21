<div wire:poll.5s class="p-6">
    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pesanan & RFQ</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola Pengajuan Pembelian dan buat Request for Quotation ke Supplier</p>
    </div>

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- SECTION 1: ANTREAN PR (PENGGANTI TOMBOL TAMBAH) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
        <div class="bg-blue-50/50 border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Antrean Pengajuan Pembelian (Menunggu Diproses)
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">ID PR</th>
                        <th class="px-6 py-4 text-left">Tgl Pengajuan</th>
                        <th class="px-6 py-4 text-left">Tujuan (Proyek / Gudang)</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($listPRPending as $pr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $pr->id_pengajuan }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($pr->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pr->permintaanProyek->proyek->nama_proyek ?? 'Restok Gudang Utama' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="createFromPR('{{ $pr->id_pengajuan }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    Buat RFQ
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic font-medium">Semua Pengajuan Pembelian telah diproses. Tidak ada antrean.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 2: TABEL RIWAYAT PESANAN --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">Daftar Riwayat RFQ / Pesanan</h2>
            <div class="w-full md:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. RFQ..." class="pl-9 w-full border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. RFQ</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl. Pesanan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ref. PR</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($listPesanan as $psn)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $psn->nomor_pesanan }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($psn->tanggal_pesanan)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $psn->supplier->nama_supplier ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $psn->id_pengajuan }}</td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $statusClass = match($psn->status_pesanan) {
                                        'Draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'Proses Negosiasi' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Berlanjut ke Kontrak' => 'bg-green-100 text-green-800 border-green-200',
                                        'Dibatalkan' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                                    {{ $psn->status_pesanan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
    {{-- Tombol PDF/Cetak (Bisa diakses siapa saja, tapi bisa mengubah status Draft) --}}
    <button wire:click="cetakPDF('{{ $psn->id_pesanan }}')" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded-md border border-blue-200 transition-colors mx-1" title="Cetak RFQ">
        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Cetak RFQ
    </button>
                              @if($psn->status_pesanan === 'Draft')
        {{-- Hanya muncul jika statusnya Draft DAN user yang login adalah pembuatnya --}}
        @if($psn->id_user_pengadaan == auth()->user()->id_user)
            <button wire:click="edit('{{ $psn->id_pesanan }}')" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1.5 rounded-md border border-yellow-200 transition-colors mx-1">
                Edit
            </button>
            <button wire:click="delete('{{ $psn->id_pesanan }}')" wire:confirm="Yakin menghapus RFQ ini?" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-md border border-red-200 transition-colors mx-1">
                Hapus
            </button>
        @else
            {{-- Pesan opsional jika user lain melihat RFQ Draft milik temannya --}}
            <span class="text-gray-400 italic text-xs ml-2">Read-Only</span>
        @endif
    @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data Pesanan (RFQ) yang dibuat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $listPesanan->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL BUAT/EDIT RFQ --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ $isEditMode ? 'Edit Request for Quotation (RFQ)' : 'Buat Request for Quotation (RFQ)' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto bg-white flex-1">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Pesanan</label>
                            <input type="date" wire:model="tanggal_pesanan" 
                                   @if(!$isEditMode) min="{{ date('Y-m-d') }}" @endif
                                   class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @error('tanggal_pesanan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- DIUBAH: Bukan lagi dropdown, tapi sekadar teks baca saja berdasarkan data yang diteruskan --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Referensi PR</label>
                            <input type="text" readonly value="{{ $id_pengajuan }}" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm bg-gray-100 text-gray-500 font-bold cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Supplier</label>
                            <select wire:model="id_supplier" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($listSupplier as $sup)
                                    <option value="{{ $sup->id_supplier }}">{{ $sup->nama_supplier }}</option>
                                @endforeach
                            </select>
                            @error('id_supplier') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-2 flex justify-between items-end border-b border-gray-200 pb-2">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Pilih Material & Tentukan Qty Pesan</h3>
                    </div>
                    
                    @error('items') 
                        <div class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm mb-4 mt-2 border border-red-100">{{ $message }}</div>
                    @enderror

                    <div class="space-y-3 mt-4">
                        @foreach($items as $index => $item)
                        <div class="flex items-center gap-4 p-4 rounded-xl border transition-all duration-200 {{ $item['selected'] ? 'border-blue-300 bg-blue-50/30 ring-1 ring-blue-100' : 'border-gray-200 bg-gray-50 opacity-60 grayscale-[50%]' }}">
                            <div class="pt-1">
                                <input type="checkbox" wire:model.live="items.{{ $index }}.selected" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $item['nama_material'] }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-gray-200 text-gray-700 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Minta: {{ $item['jumlah_asal'] }} {{ $item['satuan'] }}</span>
                                </div>
                            </div>
                            <div class="w-32 text-right">
                                <label class="text-[10px] font-bold text-gray-500 block uppercase tracking-wider mb-1">Qty Dipesan</label>
                                <div class="flex shadow-sm rounded-md">
                                    <input type="number" wire:model="items.{{ $index }}.jumlah_pesan" min="1" 
                                           class="w-full border-gray-300 rounded-l-md text-sm py-1.5 px-2 text-center focus:ring-blue-500 focus:border-blue-500 {{ !$item['selected'] ? 'bg-gray-100 text-gray-400' : 'bg-white' }}"
                                           {{ !$item['selected'] ? 'disabled' : '' }}>
                                    <span class="inline-flex items-center px-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-100 text-xs font-bold text-gray-600">
                                        {{ $item['satuan'] }}
                                    </span>
                                </div>
                                @error("items.$index.jumlah_pesan") <span class="text-red-500 text-[10px] mt-1 block text-left">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end space-x-3 mt-8 pt-5 border-t border-gray-200">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-5 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg shadow-md transition-colors flex items-center gap-2" @if(empty($items)) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan & Buat Draf RFQ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>