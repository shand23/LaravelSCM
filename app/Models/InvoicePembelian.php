<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePembelian extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'invoice_pembelian';

    // Tentukan primary key
    protected $primaryKey = 'id_invoice';

    // Karena primary key bukan integer auto-increment, matikan incrementing
    public $incrementing = false;

    // Tipe data primary key adalah string
    protected $keyType = 'string';

    // Kolom-kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'id_invoice',
        'id_kontrak',
        'id_user', // <--- TAMBAHKAN INI
        'nomor_invoice_supplier',
        'tanggal_invoice',
        'jatuh_tempo',
        'total_tagihan',
        'status_invoice',
        'file_invoice',
        'catatan',
        'alasan_batal' 
    ];

    /**
     * Logika Auto Generate ID saat data baru akan disimpan (creating)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Hanya buat ID jika belum diisi
            if (empty($model->id_invoice)) {
                // Format: INV-YYMM- (Contoh: INV-2603-)
                $prefix = 'INV-' . date('ym') . '-';
                
                // Cari data terakhir di bulan ini
                $lastInvoice = self::where('id_invoice', 'LIKE', $prefix . '%')
                                    ->orderBy('id_invoice', 'desc')
                                    ->first();

                if ($lastInvoice) {
                    $lastNumber = (int) substr($lastInvoice->id_invoice, -4);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $model->id_invoice = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke tabel Kontrak / PO
     */
    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'id_kontrak', 'id_kontrak');
    }

    /**
     * Relasi ke tabel Users (Pembuat/Admin)
     * Menggunakan foreign key 'id_user' yang merujuk ke 'id_user' di tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}