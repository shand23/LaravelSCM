<div class="p-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Penerimaan Material (QC)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola Proses Penerimaan dan Inspeksi Fisik Barang Masuk</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Proses Material Masuk
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
                   placeholder="Cari ID Penerimaan / DO..." 
                   class="pl-10 w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Penerimaan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ref DO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status QC</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi / Info</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($listPenerimaan as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $p->id_penerimaan }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->id_pengiriman }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($p->tanggal_terima)->format('d M Y') }}
                        </div>
                        <div class="text-[11px] text-gray-500 mt-1 font-medium">Petugas: {{ $p->user->nama_lengkap ?? $p->id_user_penerima }}</div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @php
                            $statusClass = match($p->status_penerimaan) {
                                'Diterima Penuh' => 'bg-green-100 text-green-800 border-green-200',
                                'Diterima Sebagian' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'Return' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                            {{ strtoupper($p->status_penerimaan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-medium">
                        {{-- Menyesuaikan gaya tombol dengan tombol Bukti Retur di Pengiriman --}}
                        @if(in_array($p->status_penerimaan, ['Return', 'Diterima Sebagian']))
                            <button wire:click="cekRiwayat('{{ $p->id_penerimaan }}')" class="text-purple-600 hover:text-purple-900 bg-purple-50 px-3 py-1.5 rounded-md border border-purple-200 transition inline-flex items-center gap-1 shadow-sm font-bold" title="Cek Riwayat Barang Rusak">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Cek Retur
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-md border border-gray-200">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Kondisi Baik
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada riwayat penerimaan material.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $listPenerimaan->links() }}
        </div>
    </div>

    {{-- MODAL PROSES PENERIMAAN --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden my-8">
            
            {{-- Header Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Inspeksi & QC Material Masuk
                    </h2>
                    <p class="text-[11px] text-gray-500 font-medium mt-1">Pastikan jumlah fisik dan kualitas sesuai dengan surat jalan (DO).</p>
                </div>
                <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 overflow-y-auto bg-white flex-1">
                
                {{-- Form Header --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Nomor DO Tiba</label>
                        <select wire:model.live="id_pengiriman" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Surat Jalan --</option>
                            @foreach($listPengirimanDO as $do)
                                <option value="{{ $do->id_pengiriman }}">{{ $do->id_pengiriman }} - Dikirim: {{ \Carbon\Carbon::parse($do->tanggal_berangkat)->format('d M') }}</option>
                            @endforeach
                        </select>
                        @error('id_pengiriman') <span class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Terima</label>
                        <input type="date" wire:model="tanggal_terima" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Kesimpulan Penilaian QC</label>
                        <div @class([
                            'w-full rounded-md shadow-sm border px-3 py-2 text-sm font-bold border-gray-300',
                            'bg-green-50 text-green-700' => $status_penerimaan == 'Diterima Penuh',
                            'bg-amber-50 text-amber-700' => $status_penerimaan != 'Diterima Penuh',
                        ])>
                            {{ strtoupper($status_penerimaan) }}
                        </div>
                    </div>
                </div>

                @if($id_pengiriman)
                {{-- Form Rincian Material --}}
                <div class="mb-2 flex justify-between items-end border-b border-gray-200 pb-2">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Rincian Fisik & Penempatan</h3>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Info Material & Qty DO</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-green-600 uppercase tracking-wider w-24">Bagus</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-red-600 uppercase tracking-wider w-24">Rusak</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Rak</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alasan Retur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($detailTerima as $key => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 text-sm">{{ $item['nama_material'] }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] text-gray-500 font-bold uppercase">{{ $item['nama_kategori'] ?? $item['kategori'] ?? 'Umum' }}</span>
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 font-bold">Qty DO: {{ $item['qty_dikirim'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" 
                                           wire:model.live.debounce.300ms="detailTerima.{{ $key }}.jumlah_bagus" 
                                           min="0" max="{{ $item['qty_dikirim'] }}" oninput="this.value = Math.abs(this.value)"
                                           class="w-full border-gray-300 rounded-md shadow-sm border px-2 py-1.5 text-sm font-bold text-center focus:ring-green-500 focus:border-green-500 text-green-700 bg-green-50">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" 
                                           wire:model.live.debounce.300ms="detailTerima.{{ $key }}.jumlah_rusak" 
                                           min="0" max="{{ $item['qty_dikirim'] }}" oninput="this.value = Math.abs(this.value)"
                                           class="w-full border-gray-300 rounded-md shadow-sm border px-2 py-1.5 text-sm font-bold text-center focus:ring-red-500 focus:border-red-500 text-red-700 bg-red-50">
                                </td>
                                <td class="px-4 py-3 w-64">
                                    <select wire:model="detailTerima.{{ $key }}.id_lokasi" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-1.5 text-xs focus:ring-blue-500 focus:border-blue-500 {{ (int)$item['jumlah_bagus'] === 0 ? 'bg-gray-100 text-gray-400' : 'bg-white font-medium' }}" {{ (int)$item['jumlah_bagus'] === 0 ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Rak --</option>
                                        @foreach($listRak as $rak)
                                            <option value="{{ $rak->id_lokasi }}">{{ $rak->nama_lokasi }} (Area {{ $rak->area ?? $rak->AREA }})</option>
                                        @endforeach
                                    </select>
                                    @error("detailTerima.{$key}.id_lokasi") <span class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model.blur="detailTerima.{{ $key }}.alasan_return" placeholder="Wajib jika rusak..." class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-1.5 text-xs focus:ring-blue-500 focus:border-blue-500 {{ (int)$item['jumlah_rusak'] === 0 ? 'bg-gray-100 text-gray-400' : 'bg-white italic' }}" {{ (int)$item['jumlah_rusak'] === 0 ? 'disabled' : '' }}>
                                    @error("detailTerima.{$key}.alasan_return") <span class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span> @enderror
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Upload Bukti Rusak (Muncul dinamis) --}}
                @if($status_penerimaan !== 'Diterima Penuh')
                <div class="mt-6 mb-2 p-5 bg-red-50 border border-red-200 rounded-xl flex items-start gap-4">
                    <div class="bg-white p-2 rounded-lg border border-red-100 text-red-500 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-red-800">Upload Bukti Kerusakan / Tolakan</label>
                        <p class="text-[11px] text-red-600 mb-3 italic">Wajib dilampirkan sebagai bukti untuk proses penggantian di Pengadaan.</p>
                        <input type="file" wire:model="foto_bukti_rusak" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 transition">
                        @error('foto_bukti_rusak') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        
                        @if ($foto_bukti_rusak)
                            <div class="mt-3">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Preview:</p>
                                <img src="{{ $foto_bukti_rusak->temporaryUrl() }}" class="h-20 rounded-md shadow-sm border border-red-200 object-cover">
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                @else
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 mt-4">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    <p class="text-sm text-gray-400 font-medium">Silahkan pilih Nomor DO terlebih dahulu untuk memuat daftar material.</p>
                </div>
                @endif
            </div>

            {{-- Footer Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button wire:click="$set('isModalOpen', false)" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="store" type="button" wire:confirm="Data stok akan langsung masuk ke Gudang. Pastikan jumlah dan rak penyimpanan sudah benar. Lanjutkan?" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center gap-2">
                    <div wire:loading wire:target="store" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                    <svg wire:loading.remove wire:target="store" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Update Gudang
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL BUKTI BARANG RUSAK (RETUR) --}}
    {{-- (Format disamakan dengan style Modal Retur di Pengiriman) --}}
    {{-- ========================================================= --}}
    @if($isModalRiwayatOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Header Modal Retur --}}
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-red-800 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Informasi Barang Rusak (Penerimaan: {{ $infoRiwayatPenerimaan ?? '-' }})
                </h2>
                <button wire:click="closeRiwayatModal" class="text-red-400 hover:text-red-700 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body Modal Retur --}}
            <div class="p-6 overflow-y-auto bg-white flex-1">
                <p class="text-sm text-gray-500 mb-4">Berikut adalah rincian material yang dilaporkan rusak atau tidak sesuai saat penerimaan di lokasi.</p>
                
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Rusak</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan Petugas</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($dataRiwayatRetur ?? [] as $retur)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $retur->nama_material }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 font-bold text-center text-lg">{{ $retur->jumlah_rusak }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 italic">"{{ $retur->alasan_return ?? 'Tidak ada catatan' }}"</td>
                                <td class="px-4 py-3 text-center flex justify-center">
                                    @if($retur->foto_bukti_rusak)
                                        <a href="{{ asset('storage/' . $retur->foto_bukti_rusak) }}" target="_blank" title="Klik untuk memperbesar">
                                            <img src="{{ asset('storage/' . $retur->foto_bukti_rusak) }}" alt="Bukti Rusak" class="w-16 h-16 object-cover rounded-md shadow-sm hover:scale-110 transition-transform border border-gray-300">
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 border border-dashed border-gray-300 p-2 rounded block">No Image</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Data rincian material rusak tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Modal Retur --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button wire:click="closeRiwayatModal" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>