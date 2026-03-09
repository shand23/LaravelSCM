<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengiriman', 'id_kontrak', 'id_user_pengadaan', 'tanggal_berangkat',
        'estimasi_tanggal_tiba', 'nama_supir', 'plat_kendaraan', 'status_pengiriman'
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (KRM0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pengiriman)) {
                $last = DB::table('pengiriman')->orderBy('id_pengiriman', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_pengiriman, 3)) + 1;
                    $model->id_pengiriman = 'KRM' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_pengiriman = 'KRM0001';
                }
            }
        });
    }

    public function kontrak() { return $this->belongsTo(Kontrak::class, 'id_kontrak', 'id_kontrak'); }
    public function userPengadaan() { return $this->belongsTo(User::class, 'id_user_pengadaan', 'id_user'); }
    public function penerimaan() { return $this->hasMany(PenerimaanMaterial::class, 'id_pengiriman', 'id_pengiriman'); }
}