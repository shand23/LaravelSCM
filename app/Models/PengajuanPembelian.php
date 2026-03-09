<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengajuanPembelian extends Model
{
    protected $table = 'pengajuan_pembelian';
    protected $primaryKey = 'id_pengajuan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    // Auto-Generate ID (PR0001)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_pengajuan) {
                $last = DB::table('pengajuan_pembelian')
                    ->orderBy('id_pengajuan', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (PR0005 -> 5)
                    // 'PR' memiliki panjang 2 karakter, jadi kita potong mulai index ke-2
                    $number = intval(substr($last->id_pengajuan, 2)) + 1;
                    $model->id_pengajuan = 'PR' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_pengajuan = 'PR0001';
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_logistik', 'id_user');
    }

    public function permintaanProyek()
    {
        return $this->belongsTo(PermintaanProyek::class, 'referensi_id_permintaan', 'id_permintaan');
    }

    public function detailPengajuan()
    {
        return $this->hasMany(DetailPengajuanPembelian::class, 'id_pengajuan', 'id_pengajuan');
    }
}