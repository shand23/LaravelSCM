<div class="p-6">
    {{-- HEADER HALAMAN --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Penerimaan Gudang (Receiving)</h1>
            <p class="text-sm text-gray-500 mt-1">Inspeksi dan pencatatan kedatangan material dari Pemasok</p>
        </div>
        <button wire:click="create" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Catat Penerimaan
        </button>
    </div>

    {{-- ALERTS NOTIFIKASI SUKSES --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- KOTAK PENCARIAN --}}
    <div class="bg-white p-4 mb-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID Penerimaan..." 
                   class="pl-10 w-full border-gray-300 rounded-lg shadow-sm sm:text-sm border px-3 py-2.5 focus:ring-green-500 focus:border-green-500">
        </div>
    </div>

    {{-- TABEL DATA UTAMA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Terima / Surat Jalan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Pengiriman (DO)</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Terima & Petugas</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($listPenerimaan as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-green-600">{{ $p->id_penerimaan }}</div>
                        <div class="text-xs text-gray-500 mt-1">SJ: {{ $p->nomor_surat_jalan ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ $p->id_pengiriman }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($p->tanggal_terima)->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500 mt-1">Oleh: {{ $p->user->name ?? 'User' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @php
                            $statusClass = match($p->status_penerimaan) {
                                'Diterima Penuh' => 'bg-green-100 text-green-800 border-green-200',
                                'Diterima Sebagian' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'Return' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                            {{ $p->status_penerimaan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2.5 py-1.5 rounded-md border border-blue-100 transition inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data riwayat penerimaan material.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $listPenerimaan->links() }}
        </div>
    </div>

    {{-- MODAL FORM PENERIMAAN BARANG --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Form Inspeksi & Penerimaan Material
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 overflow-y-auto bg-white flex-1">
                
                {{-- Pesan Error Validasi --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Form Input Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">ID Pengiriman (DO Tiba)</label>
                        <select wire:model.live="id_pengiriman" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                            <option value="">-- Pilih Truk / DO --</option>
                            @foreach($listPengirimanDO as $do)
                                <option value="{{ $do->id_pengiriman }}">{{ $do->id_pengiriman }} - (Tgl Kirim: {{ \Carbon\Carbon::parse($do->tanggal_berangkat)->format('d M y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Terima Fisik</label>
                        <input type="date" wire:model="tanggal_terima" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nomor Surat Jalan Fisik</label>
                        <input type="text" wire:model="nomor_surat_jalan" placeholder="Input no. surat jalan sopir..." class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Status Penilaian</label>
                        <select wire:model="status_penerimaan" class="w-full border-gray-300 rounded-lg shadow-sm border px-3 py-2 text-sm bg-gray-50 text-gray-700 font-bold" readonly>
                            <option value="Diterima Penuh">Diterima Penuh</option>
                            <option value="Diterima Sebagian">Diterima Sebagian (Ada Retur)</option>
                            <option value="Return">Ditolak / Return Semua</option>
                        </select>
                        <small class="text-gray-500 mt-1 block">*Berubah otomatis jika input rusak > 0</small>
                    </div>
                </div>

                {{-- AREA UPLOAD FOTO (MUNCUL JIKA ADA RETUR) --}}
                @if($status_penerimaan !== 'Diterima Penuh')
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-5 shadow-sm transition-all duration-300">
                    <label class="block text-sm font-bold text-red-700 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Upload Bukti Kerusakan / Retur (Wajib)
                    </label>
                    <p class="text-xs text-red-600 mb-3">Ambil foto langsung dari kamera atau pilih dari galeri HP.</p>
                    
                    <input type="file" wire:model="foto_bukti_rusak" accept="image/*" capture="environment" 
                           class="block w-full text-sm text-gray-500 
                                  file:mr-4 file:py-2.5 file:px-4 
                                  file:rounded-md file:border-0 
                                  file:text-sm file:font-bold 
                                  file:bg-red-600 file:text-white 
                                  hover:file:bg-red-700 file:cursor-pointer">
                    
                    {{-- Indikator Loading Saat Upload Foto --}}
                    <div wire:loading wire:target="foto_bukti_rusak" class="text-sm font-medium text-red-500 mt-2 flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Sedang mengunggah foto...
                    </div>
                    
                    @error('foto_bukti_rusak') <span class="text-xs font-bold text-red-600 mt-2 block">{{ $message }}</span> @enderror

                    {{-- Preview Gambar Sebelum Disimpan --}}
                    @if ($foto_bukti_rusak)
                        <div class="mt-4">
                            <p class="text-xs text-gray-500 font-bold mb-2 uppercase">Preview Foto:</p>
                            <img src="{{ $foto_bukti_rusak->temporaryUrl() }}" class="h-40 w-auto object-cover rounded-lg border-2 border-red-200 shadow-sm">
                        </div>
                    @endif
                </div>
                @endif

                {{-- TABEL DINAMIS: RINCIAN MATERIAL DARI DO --}}
                @if($id_pengiriman && count($detailTerima) > 0)
                <div class="mt-2 border border-gray-200 rounded-xl overflow-hidden shadow-sm bg-white">
                    <div class="bg-green-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-green-800 text-sm flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Pengecekan Fisik Barang
                        </span>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qty DO</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-green-600 uppercase">Kondisi Bagus</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-red-600 uppercase">Kondisi Rusak</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Alasan Retur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($detailTerima as $idDet => $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                                        {{ $item['nama_material'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center font-bold bg-gray-50">
                                        {{ $item['qty_dikirim'] }}
                                    </td>
                                    <td class="px-4 py-3 w-28">
                                        <input type="number" wire:model.blur="detailTerima.{{ $idDet }}.jumlah_bagus" min="0" class="w-full border-gray-300 rounded-md shadow-sm border px-2 py-1.5 text-sm text-center focus:ring-green-500 focus:border-green-500">
                                    </td>
                                    <td class="px-4 py-3 w-28">
                                        <input type="number" wire:model.blur="detailTerima.{{ $idDet }}.jumlah_rusak" min="0" class="w-full border-red-300 rounded-md shadow-sm border px-2 py-1.5 text-sm text-center focus:ring-red-500 focus:border-red-500 bg-red-50">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model.blur="detailTerima.{{ $idDet }}.alasan_return" placeholder="Isi jika ada rusak..." class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-1.5 text-sm focus:ring-green-500 focus:border-green-500">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif($id_pengiriman)
                <div class="mt-6 p-5 text-center bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-gray-500 text-sm italic">Memuat rincian barang...</p>
                </div>
                @endif

            </div>

            {{-- Footer Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button wire:click="closeModal" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="store" type="button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Data Penerimaan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>