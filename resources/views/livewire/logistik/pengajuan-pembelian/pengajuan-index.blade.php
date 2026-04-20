<div>
    {{-- HEADER --}}
    <div class="flex justify-between mb-4 items-center">
        <h1 class="text-xl font-bold text-gray-800">Daftar Pengajuan Pembelian (PR)</h1>
        <button wire:click="create" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            + Buat PR Baru
        </button>
    </div>

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTER & SEARCH --}}
    <div class="bg-white p-4 mb-4 rounded-lg shadow-sm border border-gray-100">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari ID PR atau ID Permintaan..." 
                   class="w-full border-gray-300 rounded-md shadow-sm sm:text-sm border px-3 py-2">
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID PR</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref. Permintaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status PR</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($pengajuans as $pr)
                    <tr class="hover:bg-gray-50">
                        {{-- Bagian ID PR & Keterangan User --}}
                    <td class="px-6 py-4">
    <div class="text-sm font-bold text-blue-600">{{ $pr->id_pengajuan }}</div>
    <div class="text-xs text-gray-500 mt-1">
        Pembuat: <span class="font-medium text-gray-700">
            {{ $pr->user->nama_lengkap ?? 'User Tidak Ditemukan' }}
        </span>
    </div>
</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $pr->referensi_id_permintaan ?? 'RESTOK GUDANG' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pr->permintaanProyek->proyek->nama_proyek ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $statusClass = match($pr->status_pengajuan) {
                                    'Menunggu Pengadaan' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'Proses RFQ' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'PO Dibuat' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Selesai' => 'bg-green-100 text-green-800 border-green-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                                {{ $pr->status_pengajuan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium">
                            <button wire:click="show('{{ $pr->id_pengajuan }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200 mx-1">Detail</button>
                            
                            {{-- Cek: Apakah saya pemiliknya DAN apakah statusnya masih boleh diedit/hapus? --}}
@if($pr->id_user_logistik == auth()->user()->id_user && in_array($pr->status_pengajuan, ['Draft', 'Menunggu', 'Menunggu Pengadaan']))
    <button wire:click="edit('{{ $pr->id_pengajuan }}')" 
            class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded-md border border-yellow-200 mx-1">
        Edit
    </button>
    <button wire:click="delete('{{ $pr->id_pengajuan }}')" 
            wire:confirm="Yakin membatalkan PR ini?" 
            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md border border-red-200 mx-1">
        Hapus
    </button>
@else
    {{-- Opsional: Jika bukan pemilik atau status sudah diproses, tombol tidak muncul atau bisa beri tanda --}}
    <span class="text-gray-400 text-xs italic">Akses Terkunci</span>
@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data pengajuan pembelian (PR).</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">{{ $pengajuans->links() }}</div>
    </div>

    {{-- MODAL CREATE/EDIT --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Pengajuan (PR)' : 'Buat Pengajuan Pembelian (PR)' }}</h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Referensi Permintaan Proyek</label>
                            <select wire:model.live="referensi_id_permintaan" class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 bg-white focus:ring-blue-500" {{ $isEditMode ? 'disabled' : '' }}>
                                <option value="">-- Kosongkan Jika Restok Murni --</option>
                                @foreach($listPermintaanMasuk as $req) 
                                    <option value="{{ $req->id_permintaan }}">
                                        {{ $req->id_permintaan }} - {{ $req->proyek->nama_proyek }}
                                    </option> 
                                @endforeach
                            </select>
                            @error('referensi_id_permintaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal PR</label>
                            
                            {{-- Atribut min dan max dipasang dinamis di sini --}}
                            <input type="date" wire:model="tanggal_pengajuan" 
                                   min="{{ $min_date }}" 
                                   @if($max_date) max="{{ $max_date }}" @endif
                                   class="w-full border-gray-300 rounded-md shadow-sm border px-3 py-2 focus:ring-blue-500">
                            
                            {{-- Teks Bantuan --}}
                            <p class="text-[10px] text-gray-500 mt-1">
                                Min: <span class="font-bold text-blue-600">{{ \Carbon\Carbon::parse($min_date)->format('d M Y') }}</span> 
                                @if($max_date) | Max: <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($max_date)->format('d M Y') }}</span> @endif
                            </p>
                            
                            @error('tanggal_pengajuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- FORM TAMBAH MANUAL (Hanya tampil jika Referensi Kosong / Restok Murni) --}}
                    @if(empty($referensi_id_permintaan))
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                        <h4 class="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Material Manual (Untuk Restok Gudang)
                        </h4>
                        <div class="flex gap-3 items-start">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Pilih Material</label>
                                <select wire:model="selected_material_id" class="w-full border-blue-300 rounded-md shadow-sm border px-3 py-2 text-sm focus:ring-blue-500 bg-white">
                                    <option value="">-- Cari Material --</option>
                                    @foreach($listMaterial as $mat)
                                        <option value="{{ $mat->id_material }}">{{ $mat->id_material }} - {{ $mat->nama_material }}</option>
                                    @endforeach
                                </select>
                                @error('selected_material_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Qty Beli</label>
                                <input type="number" wire:model="jumlah_manual" min="1" class="w-full border-blue-300 rounded-md shadow-sm border px-3 py-2 text-sm text-center">
                                @error('jumlah_manual') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="pt-5">
                                <button type="button" wire:click="addManualItem" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-md text-sm transition-colors shadow-sm">Tambahkan</button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3 flex justify-between items-end border-b pb-2">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Daftar Material yang akan dibeli</h3>
                    </div>
                    
                    @if(!empty($items))
                    <div class="space-y-3 max-h-[40vh] overflow-y-auto p-1 mb-4">
                        @foreach($items as $index => $item)
                        <div class="flex gap-4 items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">{{ $item['nama_material'] }}</p>
                            </div>
                            <div class="w-40">
                                <label class="text-[10px] uppercase font-bold text-gray-500 block mb-1">Qty Beli</label>
                                <div class="flex">
                                    <input type="number" wire:model="items.{{ $index }}.jumlah_minta_beli" min="1" class="w-full border-gray-300 rounded-l-md text-sm border px-2 py-1 text-center" {{ !empty($referensi_id_permintaan) ? 'readonly bg-gray-100' : '' }}>
                                    <span class="inline-flex items-center px-2 rounded-r-md border border-l-0 bg-gray-200 text-xs font-bold text-gray-600">
                                        {{ $item['satuan'] ?? '-' }}
                                    </span>
                                </div>
                                @error("items.$index.jumlah_minta_beli") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            @if(empty($referensi_id_permintaan))
                            <div class="pt-4">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded border border-red-100" title="Hapus Item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500 text-sm border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                        @if(empty($referensi_id_permintaan))
                            Daftar material kosong. Silakan tambahkan material secara manual di atas.
                        @else
                            Pilih referensi permintaan di atas untuk memuat daftar material otomatis.
                        @endif
                    </div>
                    @endif

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button" wire:click="closeModal" class="bg-white border hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors" @if(empty($items)) disabled @endif>
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Kirim PR ke Pengadaan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL TETAP SEPERTI SEBELUMNYA --}}
    @if($isDetailOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <div class="bg-gray-100 px-6 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    Detail Pengajuan (PR): <span class="text-green-600">{{ $selectedPengajuan->id_pengajuan }}</span>
                </h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Referensi Proyek</p>
                        <p class="font-bold text-gray-800">{{ $selectedPengajuan->referensi_id_permintaan ?? 'RESTOK GUDANG' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Dibuat Oleh</p>
                        <p class="font-bold text-gray-800">{{ $selectedPengajuan->user->nama_lengkap ?? '-' }}</p>
                    </div>
                </div>
                
                <h3 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Daftar Material PR</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Material</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Satuan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qty Dibeli</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($selectedPengajuan->detailPengajuan as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $detail->material->nama_material }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-500 font-medium bg-gray-50">{{ $detail->material->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-green-600">{{ $detail->jumlah_minta_beli }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-end mt-8">
                    <button wire:click="closeModal" class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-6 py-2 rounded-lg shadow transition-colors">Tutup Jendela</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>