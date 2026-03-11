<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengirimanDetail extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_detail';
    protected $primaryKey = 'id_pengiriman_detail';
    
    // Penting: Matikan increment bawaan Laravel karena kita pakai Custom String
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengiriman_detail', 'id_pengiriman', 'id_detail_kontrak', 'jumlah_dikirim'
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (KRD0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pengiriman_detail)) {
                $last = DB::table('pengiriman_detail')->orderBy('id_pengiriman_detail', 'desc')->first();
                if ($last) {
                    // Ambil angka setelah "KRD"
                    $number = intval(substr($last->id_pengiriman_detail, 3)) + 1;
                    $model->id_pengiriman_detail = 'KRD' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_pengiriman_detail = 'KRD0001';
                }
            }
        });
    }

    // RELASI KEMBALI KE HEADER PENGIRIMAN
    public function pengiriman() { 
        return $this->belongsTo(Pengiriman::class, 'id_pengiriman', 'id_pengiriman'); 
    }

    // RELASI KE DETAIL KONTRAK / PO
    public function detailKontrak() { 
        
        return $this->belongsTo(DetailKontrak::class, 'id_detail_kontrak', 'id_detail_kontrak');
    }
}