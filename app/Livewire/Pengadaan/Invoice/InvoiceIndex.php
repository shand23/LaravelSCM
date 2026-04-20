<?php

namespace App\Livewire\Pengadaan\Invoice;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\InvoicePembelian;
use App\Models\Kontrak;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // 1. Tambahkan ini
use Illuminate\Support\Facades\DB;

class InvoiceIndex extends Component
{
   use WithPagination;
    use WithFileUploads;

    public $isOpen = false;
    public $isEditMode = false;
    public $invoiceIdBeingUpdated = null;
    
    public $search = '';

    // TAMBAHKAN DUA BARIS INI UNTUK MODAL DETAIL
    public $isDetailOpen = false;
    public $selectedInvoice = null;

    // Form fields
    public $id_kontrak, $nomor_invoice_supplier, $tanggal_invoice, $jatuh_tempo;
    public $total_tagihan, $file_invoice_upload, $catatan;

    public $selectedKontrakData = null;

    public function render()
    {
        // 1. Ambil data invoice beserta relasi kontrak dan user pembuatnya
        $invoices = InvoicePembelian::with(['kontrak', 'user'])
            ->when($this->search, function($query) {
                $query->where('id_invoice', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_invoice_supplier', 'like', '%' . $this->search . '%')
                      ->orWhereHas('kontrak', function($q) {
                          $q->where('nomor_kontrak', 'like', '%' . $this->search . '%');
                      });
            })
            ->latest('created_at')
            ->paginate(10);
        
        // 2. Filter PO/Kontrak: Hanya yang belum di-invoice + PO yang sedang diedit (jika mode edit)
        
        $isEditMode = $this->isEditMode;
        $currentIdKontrak = $this->id_kontrak;

        $kontrakList = Kontrak::where('status_kontrak', 'Disepakati')
            ->where(function ($query) use ($isEditMode, $currentIdKontrak) {
                // Ambil kontrak yang ID-nya BELUM ADA di tabel invoice_pembelian
                $query->whereNotIn('id_kontrak', function ($subquery) {
                    $subquery->select('id_kontrak')->from('invoice_pembelian');
                });

                // PENGECUALIAN: Jika sedang dalam mode Edit, tetap tampilkan kontrak yang sedang diedit
                if ($isEditMode && $currentIdKontrak) {
                    $query->orWhere('id_kontrak', $currentIdKontrak);
                }
            })
            ->get(); 

        return view('livewire.pengadaan.invoice.invoice-index', [
            'invoices' => $invoices,
            'kontrakList' => $kontrakList,
        ])->layout('layouts.app');
    }

    // 2. Tambahkan Fungsi untuk Modal Detail
    public function showDetail($id)
    {
        $this->selectedInvoice = InvoicePembelian::with(['kontrak.supplier', 'kontrak.detailKontrak.material', 'user'])->find($id);
        $this->isDetailOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->selectedInvoice = null;
    }

  public function printInvoice($id)
    {
        $invoice = InvoicePembelian::with(['kontrak.supplier', 'kontrak.detailKontrak.material'])->find($id);

        if (!$invoice) {
            session()->flash('error', 'Data tidak ditemukan.');
            return;
        }

        $imageData = null;
        $fileType = null;

        if ($invoice->file_invoice) {
            // Cek ekstensi file, pastikan ini BUKAN pdf. (PDF tidak bisa dijadikan tag <img>)
            $extension = strtolower(pathinfo($invoice->file_invoice, PATHINFO_EXTENSION));
            $fileType = $extension;

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                
                // Gunakan path absolut dari public
                $path = public_path('storage/' . $invoice->file_invoice);
                
                // Fallback jika symlink belum dibuat (mencari ke folder laravel langsung)
                if (!file_exists($path)) {
                    $path = storage_path('app/public/' . $invoice->file_invoice);
                }

                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $imageData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        }

        // Aktifkan isRemoteEnabled agar DomPDF bisa me-render gambar dengan baik
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('livewire.pengadaan.invoice.pdf-invoice', [
                'invoice' => $invoice,
                'kontrak' => $invoice->kontrak,
                'imageData' => $imageData,
                'fileType'  => $fileType
            ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Tagihan-' . str_replace('/', '-', $invoice->id_invoice) . '.pdf');
    }

    public function updatedIdKontrak($value)
    {
        if ($value) {
            $this->selectedKontrakData = Kontrak::find($value);
            if ($this->selectedKontrakData && !$this->isEditMode) {
                $this->total_tagihan = $this->selectedKontrakData->total_nilai_kontrak;
            }
        }
    }

    // --- FITUR ACTION CEPAT ---
   public function setStatusLunas($id)
    {
        try {
            // 1. Ambil data invoice beserta relasi hingga ke Pengajuan
            $invoice = InvoicePembelian::with(['kontrak.pesanan'])->findOrFail($id);

            // 2. VALIDASI HAK AKSES
            // Hanya pembuat invoice (id_user) yang bisa menandai sebagai Lunas
            if ($invoice->id_user != Auth::user()->id_user) {
                session()->flash('error', 'Akses Ditolak: Anda hanya dapat memproses invoice yang Anda buat sendiri.');
                return;
            }

            // 3. PROSES UPDATE DENGAN TRANSAKSI
            DB::transaction(function () use ($invoice) {
                // Update status Invoice menjadi Lunas
                $invoice->update(['status_invoice' => 'Lunas']);

                // Update status Pengajuan (PR) menjadi Selesai
                // Alur: Invoice -> Kontrak -> Pesanan -> id_pengajuan
                if ($invoice->kontrak && $invoice->kontrak->pesanan && $invoice->kontrak->pesanan->id_pengajuan) {
                    \App\Models\PengajuanPembelian::where('id_pengajuan', $invoice->kontrak->pesanan->id_pengajuan)
                        ->update(['status_pengajuan' => 'Selesai']);
                }
            });

            session()->flash('message', 'Status Invoice diperbarui menjadi Lunas dan PR telah Selesai.');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function setStatusSebagian($id)
    {
        $invoice = InvoicePembelian::findOrFail($id);
        $invoice->update(['status_invoice' => 'Dibayar Sebagian']);
        session()->flash('message', 'Status Invoice diperbarui menjadi Dibayar Sebagian.');
    }

    // --- FITUR DELETE ---
    public function delete($id)
    {
        $invoice = InvoicePembelian::findOrFail($id);

        if ($invoice->id_user != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk menghapus invoice ini.');
            return;
        }

        // Proteksi: Tidak bisa hapus jika sudah ada pembayaran
        if (in_array($invoice->status_invoice, ['Lunas', 'Dibayar Sebagian'])) {
            session()->flash('error', 'Invoice yang sudah dibayar tidak dapat dihapus.');
            return;
        }

        if ($invoice->file_invoice) {
            Storage::disk('public')->delete($invoice->file_invoice);
        }

        $invoice->delete();
        session()->flash('message', 'Invoice berhasil dihapus.');
    }

    // --- FITUR EDIT ---
    public function edit($id)
    {
        $invoice = InvoicePembelian::findOrFail($id);

        if ($invoice->id_user != Auth::user()->id_user) {
            session()->flash('error', 'Akses Ditolak: Anda hanya dapat mengubah invoice yang Anda buat sendiri.');
            return;
        }

        // Proteksi: Tidak bisa edit jika sudah ada pembayaran
        if (in_array($invoice->status_invoice, ['Lunas', 'Dibayar Sebagian'])) {
            session()->flash('error', 'Invoice yang sudah dibayar tidak dapat diubah.');
            return;
        }

        $this->invoiceIdBeingUpdated = $id;
        $this->id_kontrak = $invoice->id_kontrak;
        $this->nomor_invoice_supplier = $invoice->nomor_invoice_supplier;
        $this->tanggal_invoice = $invoice->tanggal_invoice;
        $this->jatuh_tempo = $invoice->jatuh_tempo;
        $this->total_tagihan = $invoice->total_tagihan;
        $this->catatan = $invoice->catatan;
        
        $this->selectedKontrakData = Kontrak::find($invoice->id_kontrak);
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->id_kontrak = '';
        $this->nomor_invoice_supplier = '';
        $this->tanggal_invoice = '';
        $this->jatuh_tempo = '';
        $this->total_tagihan = '';
        $this->file_invoice_upload = null;
        $this->catatan = '';
        $this->selectedKontrakData = null;
        $this->invoiceIdBeingUpdated = null;
    }

    public function store()
    {
        $minTanggalInvoice = $this->selectedKontrakData ? $this->selectedKontrakData->tanggal_kontrak : '2000-01-01';

        $rules = [
            'id_kontrak' => 'required',
            'nomor_invoice_supplier' => 'required|string|max:100',
            'tanggal_invoice' => 'required|date|after_or_equal:' . $minTanggalInvoice,
            'jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'total_tagihan' => 'required|numeric|min:0',
        ];


        
        // File invoice hanya wajib diisi saat create baru
        if (!$this->isEditMode) {
            $rules['file_invoice_upload'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $this->validate($rules);

        $data = [
            'id_kontrak' => $this->id_kontrak,
            'nomor_invoice_supplier' => $this->nomor_invoice_supplier,
            'tanggal_invoice' => $this->tanggal_invoice,
            'jatuh_tempo' => $this->jatuh_tempo,
            'total_tagihan' => $this->total_tagihan,
            'catatan' => $this->catatan,
        ];

        if ($this->file_invoice_upload) {
            $data['file_invoice'] = $this->file_invoice_upload->store('invoices', 'public');
        }

        if ($this->isEditMode) {
            InvoicePembelian::find($this->invoiceIdBeingUpdated)->update($data);
            session()->flash('message', 'Invoice berhasil diperbarui.');
        } else {
            // Memasukkan ID User yang login saat data baru dibuat
            $data['id_user'] = Auth::user()->id_user; 
            $data['status_invoice'] = 'Menunggu Pembayaran';
            
            InvoicePembelian::create($data);
            session()->flash('message', 'Invoice berhasil disimpan.');
        }

        $this->closeModal();
    }

    public function downloadFile($id_invoice)
    {
        $invoice = InvoicePembelian::findOrFail($id_invoice);
        if ($invoice->file_invoice && Storage::disk('public')->exists($invoice->file_invoice)) {
            return Storage::disk('public')->download($invoice->file_invoice);
        }
        session()->flash('error', 'File tidak ditemukan.');
    }
}