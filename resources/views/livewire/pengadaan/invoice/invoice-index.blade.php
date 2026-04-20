<div class="p-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Invoice Pembelian</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola Tagihan dan Verifikasi Bukti Pembayaran dari Supplier</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Invoice
        </button>
    </div>

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <p>{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- BARIS PENCARIAN (SEARCH BAR) --}}
   <div class="bg-white p-4 mb-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3 relative">
    
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live="search" type="text" placeholder="Cari ID Invoice atau No. Supplier..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
        </div>
    </div>
    
 {{-- TABEL (Style disamakan dengan Kontrak) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID / Kontrak</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pembuat</th> 
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    {{-- Kolom Aksi Cepat (Sekarang menampung semua tombol) --}}
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $inv)
                @php $isLocked = in_array($inv->status_invoice, ['Lunas', 'Dibayar Sebagian']); @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $inv->id_invoice }}</div>
                        <div class="text-xs text-blue-600 font-medium">PO: {{ $inv->kontrak->nomor_kontrak ?? $inv->id_kontrak }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $inv->nomor_invoice_supplier }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $inv->user->nama_lengkap ?? 'Sistem' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full 
                            {{ $inv->status_invoice === 'Lunas' ? 'bg-green-100 text-green-700' : 
                               ($inv->status_invoice === 'Dibayar Sebagian' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $inv->status_invoice }}
                        </span>
                    </td>
                    
                    {{-- KOLOM AKSI CEPAT --}}
                    <td class="px-6 py-4 text-center text-sm font-medium">
                        <div class="flex items-center justify-center flex-wrap gap-2">
                            
                            {{-- Tombol Ubah Status --}}
                            @if($inv->status_invoice !== 'Lunas')
                                <button wire:click="setStatusLunas('{{ $inv->id_invoice }}')" class="bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-bold hover:bg-green-700 transition">Lunas</button>
                                @if($inv->status_invoice !== 'Dibayar Sebagian')
                                    <button wire:click="setStatusSebagian('{{ $inv->id_invoice }}')" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-bold hover:bg-blue-700 transition">Sebagian</button>
                                @endif
                            @endif

                            {{-- Tombol Detail --}}
                            <button wire:click="showDetail('{{ $inv->id_invoice }}')" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1.5 rounded-md border border-blue-100 transition flex items-center gap-1" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Detail
                            </button>

                            {{-- Tombol PDF --}}
                            <button wire:click="printInvoice('{{ $inv->id_invoice }}')" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1.5 rounded-md border border-red-100 transition flex items-center gap-1" title="Cetak PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                PDF
                            </button>

                          @if(!$isLocked)
    {{-- CEK HAK AKSES: Hanya pembuat yang bisa melihat tombol Edit & Hapus --}}
    @if($inv->id_user == auth()->user()->id_user)
        {{-- Tombol Edit --}}
        <button wire:click="edit('{{ $inv->id_invoice }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1.5 rounded-md border border-indigo-100 transition flex items-center gap-1" title="Edit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit
        </button>
        
        {{-- Tombol Hapus --}}
        <button wire:click="delete('{{ $inv->id_invoice }}')" onclick="confirm('Yakin ingin menghapus?') || event.stopImmediatePropagation()" class="text-pink-600 hover:text-pink-900 bg-pink-50 px-2 py-1.5 rounded-md border border-pink-100 transition flex items-center gap-1" title="Hapus">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Hapus
        </button>
    @else
        {{-- Tampilan jika staf lain yang melihat (Bukan Pembuat) --}}
        <span class="text-gray-400 italic text-xs px-2">Read-Only</span>
    @endif
@else
    {{-- Status Terkunci (Misal: Sudah Lunas) --}}
    <span class="inline-flex items-center gap-1 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-md border border-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        Terkunci
    </span>
@endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data invoice yang tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>

    

    {{-- Modal Form --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $isEditMode ? 'Edit' : 'Input' }} Invoice Pembelian</h3>
                        <div class="space-y-4">
                            
                            {{-- Dropdown Kontrak --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih PO / Kontrak *</label>
                                <select wire:model.live="id_kontrak" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" {{ $isEditMode ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kontrak --</option>
                                    @foreach($kontrakList as $kontrak)
                                        <option value="{{ $kontrak->id_kontrak }}">{{ $kontrak->id_kontrak }} - {{ $kontrak->nomor_kontrak }}</option>
                                    @endforeach
                                </select>
                                @error('id_kontrak') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- TAMPILAN DETAIL KONTRAK --}}
                            @if($selectedKontrakData)
                            <div class="p-4 mt-3 bg-blue-50 border border-blue-200 rounded-md shadow-sm text-sm text-gray-700">
                                <h4 class="font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Detail Informasi PO / Kontrak
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block text-xs text-gray-500">Nomor Kontrak</span>
                                            <span class="font-semibold">{{ $selectedKontrakData->nomor_kontrak ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-xs text-gray-500">Tanggal Kontrak</span>
                                            <span class="font-semibold">{{ $selectedKontrakData->tanggal_kontrak ? \Carbon\Carbon::parse($selectedKontrakData->tanggal_kontrak)->translatedFormat('d F Y') : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-xs text-gray-500">Supplier</span>
                                            {{-- Menampilkan nama supplier jika relasinya ada, jika tidak, tampilkan ID-nya --}}
                                            <span class="font-semibold">{{ $selectedKontrakData->supplier->nama_supplier ?? $selectedKontrakData->id_supplier }}</span>
                                        </div>
                                        
                                    </div>

                                    <div class="bg-white p-3 rounded border border-blue-100 space-y-1 text-xs shadow-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Harga Negosiasi:</span> 
                                            <span>Rp {{ number_format($selectedKontrakData->total_harga_negosiasi, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-red-600">
                                            <span>Diskon:</span> 
                                            <span>- Rp {{ number_format($selectedKontrakData->total_diskon, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ongkos Kirim:</span> 
                                            <span>Rp {{ number_format($selectedKontrakData->total_ongkir, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">PPN:</span> 
                                            <span>Rp {{ number_format($selectedKontrakData->total_ppn, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between font-bold text-sm text-green-700 border-t border-gray-200 pt-2 mt-2">
                                            <span>Total Keseluruhan:</span> 
                                            <span>Rp {{ number_format($selectedKontrakData->total_nilai_kontrak, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. Invoice Supplier *</label>
                                <input type="text" wire:model="nomor_invoice_supplier" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('nomor_invoice_supplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Invoice *</label>
                                    <input type="date" wire:model.live="tanggal_invoice" 
                                           min="{{ $selectedKontrakData->tanggal_kontrak ?? '' }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('tanggal_invoice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jatuh Tempo *</label>
                                    <input type="date" wire:model="jatuh_tempo" 
                                           min="{{ $tanggal_invoice }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('jatuh_tempo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Tagihan (Rp) *</label>
                                <input type="number" wire:model="total_tagihan" min="0" oninput="this.value = Math.abs(this.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <span class="text-xs text-gray-500 mt-1 block">Pastikan nominal sesuai dengan Total Keseluruhan Kontrak atau tagihan parsial dari Supplier.</span>
                                @error('total_tagihan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">File Invoice {{ $isEditMode ? '(Biarkan kosong jika tidak diubah)' : '*' }}</label>
                                <input type="file" wire:model="file_invoice_upload" accept=".pdf,.jpg,.png" class="mt-1 block w-full text-sm">
                                <div wire:loading wire:target="file_invoice_upload" class="text-xs text-blue-500 mt-1">Mengunggah...</div>
                                @error('file_invoice_upload') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                <textarea wire:model="catatan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 sm:ml-3 sm:w-auto">
                            {{ $isEditMode ? 'Update' : 'Simpan' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md bg-white px-4 py-2 text-gray-700 border hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

{{-- MODAL DETAIL --}}
    @if($isDetailOpen && $selectedInvoice)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" wire:click="closeDetail"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all z-10">
            
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Detail Permohonan Pembayaran: {{ $selectedInvoice->id_invoice }}</h3>
                <button wire:click="closeDetail" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-4 border-b pb-2">Rincian Tagihan</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">No. Inv Supplier:</span> <span class="font-semibold text-gray-800">{{ $selectedInvoice->nomor_invoice_supplier }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Tanggal Invoice:</span> <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($selectedInvoice->tanggal_invoice)->format('d M Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Jatuh Tempo:</span> <span class="font-semibold text-red-600">{{ \Carbon\Carbon::parse($selectedInvoice->jatuh_tempo)->format('d M Y') }}</span></div>
                            <div class="flex justify-between pt-2 border-t"><span class="text-gray-500 font-bold">Total Tagihan:</span> <span class="text-lg font-bold text-blue-700">Rp {{ number_format($selectedInvoice->total_tagihan, 0, ',', '.') }}</span></div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-4 border-b pb-2">Referensi Kontrak (PO)</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">No. Kontrak:</span> <span class="font-semibold text-gray-800">{{ $selectedInvoice->kontrak->nomor_kontrak }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Supplier:</span> <span class="font-semibold text-gray-800 text-right">{{ $selectedInvoice->kontrak->supplier->nama_supplier }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Nilai PO:</span> <span class="font-semibold text-gray-800">Rp {{ number_format($selectedInvoice->kontrak->total_nilai_kontrak, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500 text-xs">Catatan:</span> <span class="text-xs text-gray-600 italic">{{ $selectedInvoice->catatan ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700">Item yang Dipesan</h4>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Material</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($selectedInvoice->kontrak->detailKontrak as $item)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-800">{{ $item->material->nama_material }}</td>
                                <td class="px-5 py-3 text-sm text-center text-gray-700">{{ $item->jumlah_final }} {{ $item->material->satuan }}</td>
                                <td class="px-5 py-3 text-sm text-right text-gray-700">Rp {{ number_format($item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-sm text-right font-semibold text-gray-900">Rp {{ number_format($item->jumlah_final * $item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bukti Fisik Invoice Supplier
                    </h4>
                    @if($selectedInvoice->file_invoice)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $selectedInvoice->file_invoice) }}" class="max-h-[500px] mx-auto rounded-lg shadow-inner border border-gray-100 object-contain bg-gray-200">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ asset('storage/' . $selectedInvoice->file_invoice) }}" target="_blank" class="bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm font-medium">Lihat Ukuran Penuh</a>
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg">
                            <p class="text-gray-400 italic">Tidak ada lampiran foto bukti invoice.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end">
                <button wire:click="closeDetail" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-2 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

   

</div>