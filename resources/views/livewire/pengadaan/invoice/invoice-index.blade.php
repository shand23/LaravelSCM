<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Manajemen Invoice Pembelian</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
            + Tambah Invoice
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID / Kontrak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Tagihan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembuat</th> 
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi Cepat</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Manajemen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $inv)
                @php $isLocked = in_array($inv->status_invoice, ['Lunas', 'Dibayar Sebagian']); @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="font-bold">{{ $inv->id_invoice }}</div>
                        <div class="text-gray-500 text-xs">PO: {{ $inv->id_kontrak }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inv->nomor_invoice_supplier }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $inv->user->nama_lengkap ?? 'Sistem / Dihapus' }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full 
                            {{ $inv->status_invoice === 'Lunas' ? 'bg-green-100 text-green-800' : 
                               ($inv->status_invoice === 'Dibayar Sebagian' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $inv->status_invoice }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs space-x-1">
                        @if($inv->status_invoice !== 'Lunas')
                            <button wire:click="setStatusLunas('{{ $inv->id_invoice }}')" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Lunas</button>
                            @if($inv->status_invoice !== 'Dibayar Sebagian')
                                <button wire:click="setStatusSebagian('{{ $inv->id_invoice }}')" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Sebagian</button>
                            @endif
                        @else
                            <span class="text-gray-400 italic">Selesai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        @if($inv->file_invoice)
                            <button wire:click="downloadFile('{{ $inv->id_invoice }}')" class="text-gray-600 hover:text-black" title="Unduh File"><i class="fas fa-download"></i> File</button>
                        @endif

                        @if(!$isLocked)
                            <button wire:click="edit('{{ $inv->id_invoice }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            <button wire:click="delete('{{ $inv->id_invoice }}')" wire:confirm="Yakin ingin menghapus invoice ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                        @else
                            <span class="text-gray-400 cursor-not-allowed" title="Sudah ada pembayaran, data dikunci">Locked</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 italic">Data kosong.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $invoices->links() }}</div>
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
</div>