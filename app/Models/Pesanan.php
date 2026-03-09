<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pesanan', 
        'id_pengajuan', 
        'id_supplier', 
        'id_user_pengadaan', 
        'nomor_pesanan', 
        'tanggal_pesanan', 
        'status_pesanan'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_pesanan)) {
                $last = DB::table('pesanan')->orderBy('id_pesanan', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_pesanan, 3)) + 1;
                    $model->id_pesanan = 'RFQ' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_pesanan = 'RFQ0001';
                }
            }
        });
    }

    // Relasi
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPembelian::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function userPengadaan()
    {
        return $this->belongsTo(User::class, 'id_user_pengadaan', 'id_user');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }
    
    public function kontrak()
    {
        return $this->hasOne(Kontrak::class, 'id_pesanan', 'id_pesanan');
    }
}