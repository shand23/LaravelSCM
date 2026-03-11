<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPenerimaan extends Model
{
    use HasFactory;

    // Konfigurasi Tabel
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'id_detail_terima';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi
    protected $fillable = [
        'id_detail_terima',
        'id_penerimaan',
        'id_pengiriman_detail',
        'id_detail_kontrak',
        'jumlah_bagus',
        'jumlah_rusak',
        'alasan_return',
        'foto_bukti_rusak',
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (DTR0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_detail_terima)) {
                $last = DB::table('detail_penerimaan')->orderBy('id_detail_terima', 'desc')->first();
                
                if ($last) {
                    // Ambil angka setelah "DTR"
                    $number = intval(substr($last->id_detail_terima, 3)) + 1;
                    $model->id_detail_terima = 'DTR' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_terima = 'DTR0001';
                }
            }
        });
    }

    // ==========================================
    // RELASI ANTAR TABEL
    // ==========================================

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanMaterial::class, 'id_penerimaan', 'id_penerimaan');
    }

    public function detail_pengiriman()
    {
        return $this->belongsTo(PengirimanDetail::class, 'id_pengiriman_detail', 'id_pengiriman_detail');
    }

    public function detail_kontrak()
    {
        return $this->belongsTo(DetailKontrak::class, 'id_detail_kontrak', 'id_detail_kontrak');
    }
}