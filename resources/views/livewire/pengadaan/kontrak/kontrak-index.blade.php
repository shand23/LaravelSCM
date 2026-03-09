<div>
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Kontrak (PO)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola Penerbitan Purchase Order ke Supplier</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat PO Baru
        </button>
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

    {{-- SEARCH --}}
    <div class="bg-white p-4 mb-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari Nomor PO..." 
                   class="pl-10 w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. PO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl. Kontrak</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($listKontrak as $k)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $k->nomor_kontrak }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($k->tanggal_kontrak)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $k->supplier->nama_supplier ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">Rp{{ number_format($k->total_nilai_kontrak, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-sm">
                            @php
                                $statusClass = match($k->status_kontrak) {
                                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'Disepakati' => 'bg-green-100 text-green-800 border-green-200',
                                    'Aktif' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Selesai' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                                {{ $k->status_kontrak }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium flex justify-center items-center gap-2">
                            
                            {{-- Tombol Detail (Selalu Tampil) --}}
                            <button wire:click="showDetail('{{ $k->id_kontrak }}')" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1.5 rounded-md border border-blue-100 transition flex items-center gap-1" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Detail
                            </button>

                            @if($k->status_kontrak !== 'Disepakati')
                                {{-- Tombol Disepakati --}}
                                <button wire:click="markAsDisepakati('{{ $k->id_kontrak }}')" wire:confirm="Yakin menyepakati PO ini? Data yang sudah disepakati TIDAK DAPAT diubah atau dihapus kembali." class="text-green-600 hover:text-green-900 bg-green-50 px-2 py-1.5 rounded-md border border-green-100 transition">
                                    Sepakati
                                </button>
                                
                                {{-- Tombol Edit --}}
                                <button wire:click="edit('{{ $k->id_kontrak }}')" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-2 py-1.5 rounded-md border border-yellow-100 transition">
                                    Edit
                                </button>
                                
                                {{-- Tombol Delete --}}
                                <button wire:click="delete('{{ $k->id_kontrak }}')" wire:confirm="Yakin ingin menghapus PO ini? RFQ terkait akan dikembalikan ke antrean negosiasi." class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1.5 rounded-md border border-red-100 transition">
                                    Hapus
                                </button>
                            @else
                                {{-- Tampilan jika sudah disepakati (Kunci & PDF) --}}
                                <span class="inline-flex items-center gap-1 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-md border border-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Terkunci
                                </span>
                                
                                {{-- Tombol PDF --}}
                                <button class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded-md border border-blue-100 flex items-center gap-1 transition" title="Cetak / Download PO">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    PDF
                                </button>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data Purchase Order (PO) yang diterbitkan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $listKontrak->links() }}
        </div>
    </div>

    {{-- MODAL BUAT/EDIT PO --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Header Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ $isEditMode ? 'Edit Purchase Order (PO)' : 'Penerbitan Purchase Order (PO)' }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Body Modal --}}
            <div class="p-6 overflow-y-auto bg-white flex-1">
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    
                    {{-- Form Header PO --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8 bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Referensi RFQ</label>
                            <select wire:model.live="id_pesanan" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}" {{ $isEditMode ? 'disabled' : '' }}>
                                <option value="">-- Pilih Antrean RFQ --</option>
                                @foreach($listRFQ as $rfq)
                                    <option value="{{ $rfq->id_pesanan }}">{{ $rfq->nomor_pesanan }} ({{ $rfq->supplier->nama_supplier ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('id_pesanan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Kontrak / PO</label>
                            <input type="date" wire:model="tanggal_kontrak" 
                                   @if(!$isEditMode) min="{{ date('Y-m-d') }}" @endif
                                   class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @error('tanggal_kontrak') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($id_pesanan)
                    {{-- Form Detail Material --}}
                    <div class="mb-2 flex justify-between items-end border-b border-gray-200 pb-2">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Detail Item & Harga Negosiasi</h3>
                    </div>
                    
                    <div class="mb-8 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Qty Final</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Satuan (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item['nama_material'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <input type="number" wire:model.live="items.{{ $index }}.jumlah_final" min="1" class="w-20 text-center border-gray-300 rounded-md text-sm py-1 focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-xs text-gray-500 font-bold uppercase">{{ $item['satuan'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model.live="items.{{ $index }}.harga_negosiasi_satuan" min="0" class="w-full border-gray-300 rounded-md text-sm py-1 focus:ring-blue-500 focus:border-blue-500">
                                        @error("items.$index.harga_negosiasi_satuan") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-800">
                                        {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Financial --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Komponen Biaya Tambahan --}}
                        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Diskon (%)</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" wire:model.live="diskon_persen" min="0" max="100" class="w-20 border-gray-300 rounded-md py-1.5 text-right font-medium focus:ring-blue-500 text-sm">
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500 border-b border-gray-200 pb-2">
                                <span>Nominal Potongan:</span>
                                <span>- Rp {{ number_format($nominal_diskon, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Ongkos Kirim (+)</label>
                                <input type="number" wire:model.live="total_ongkir" min="0" class="w-32 border-gray-300 rounded-md py-1.5 text-right font-medium focus:ring-blue-500 text-sm">
                            </div>
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Pajak PPN (+)</label>
                                <input type="number" wire:model.live="total_ppn" min="0" class="w-32 border-gray-300 rounded-md py-1.5 text-right font-medium focus:ring-blue-500 text-sm">
                            </div>
                        </div>

                        {{-- Total Grand Final --}}
                        <div class="bg-blue-600 p-6 rounded-xl shadow-md flex flex-col justify-center items-end text-white border border-blue-700">
                            <span class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-1">Total Nilai Kontrak</span>
                            <div class="text-4xl font-bold tracking-tight">
                                <span class="text-lg text-blue-300 font-medium mr-1">Rp</span>{{ number_format($total_nilai_kontrak, 0, ',', '.') }}
                            </div>
                            <p class="text-blue-200 text-[10px] mt-4 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Status RFQ akan otomatis dialihkan
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-16 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                        Silakan pilih Referensi RFQ di atas terlebih dahulu untuk memulai kalkulasi PO.
                    </div>
                    @endif

                    {{-- Footer Modal --}}
                    <div class="flex justify-end space-x-3 mt-8 pt-5 border-t border-gray-200">
                        <button type="button" wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-5 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg shadow-md transition-colors flex items-center gap-2" @if(!$id_pesanan) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            {{ $isEditMode ? 'Simpan Perubahan PO' : 'Terbitkan PO Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL KONTRAK (PO) --}}
    @if($isDetailModalOpen && $kontrakDetailData)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Header Modal Detail --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Detail Purchase Order (PO)
                </h2>
                <button wire:click="closeDetailModal" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Body Modal Detail (Scrollable) --}}
            <div class="p-6 overflow-y-auto bg-white flex-1">
                
                {{-- Info Header PO --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm">
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Nomor PO</span>
                        <span class="font-bold text-blue-700">{{ $kontrakDetailData->nomor_kontrak }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Tanggal</span>
                        <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($kontrakDetailData->tanggal_kontrak)->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Supplier</span>
                        <span class="font-bold text-gray-800">{{ $kontrakDetailData->supplier->nama_supplier ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Status</span>
                        <span class="font-bold text-gray-800">{{ $kontrakDetailData->status_kontrak }}</span>
                    </div>
                </div>

                {{-- Tabel Item Material --}}
                <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Harga Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach($kontrakDetailData->detailKontrak as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $detail->material->nama_material ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $detail->jumlah_final }} <span class="text-xs text-gray-500">{{ $detail->material->satuan ?? '' }}</span></td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($detail->harga_negosiasi_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800">
                                    Rp {{ number_format($detail->jumlah_final * $detail->harga_negosiasi_satuan, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Rincian Biaya (Financial Summary) --}}
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-2 text-sm">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Total Harga Item:</span>
                            <span class="font-semibold">Rp {{ number_format($kontrakDetailData->total_harga_negosiasi, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-red-600">
                            <span>Diskon:</span>
                            <span class="font-semibold">- Rp {{ number_format($kontrakDetailData->total_diskon, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Ongkos Kirim:</span>
                            <span class="font-semibold">+ Rp {{ number_format($kontrakDetailData->total_ongkir, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600 border-b border-gray-200 pb-2">
                            <span>Pajak (PPN):</span>
                            <span class="font-semibold">+ Rp {{ number_format($kontrakDetailData->total_ppn, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold text-blue-700 pt-1">
                            <span>Grand Total:</span>
                            <span>Rp {{ number_format($kontrakDetailData->total_nilai_kontrak, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>
            
            {{-- Footer Modal Detail --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                <button wire:click="closeDetailModal" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium px-5 py-2 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>