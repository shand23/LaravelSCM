<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenerimaanMaterial extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_material';
    protected $primaryKey = 'id_penerimaan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_penerimaan', 'id_pengiriman', 'id_user_penerima', 'tanggal_terima',
        'nomor_surat_jalan', 'status_penerimaan'
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (TRM0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_penerimaan)) {
                $last = DB::table('penerimaan_material')->orderBy('id_penerimaan', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_penerimaan, 3)) + 1;
                    $model->id_penerimaan = 'TRM' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_penerimaan = 'TRM0001';
                }
            }
        });
    }

    public function pengiriman() { return $this->belongsTo(Pengiriman::class, 'id_pengiriman', 'id_pengiriman'); }
    public function userPenerima() { return $this->belongsTo(User::class, 'id_user_penerima', 'id_user'); }
    public function detailPenerimaan() { return $this->hasMany(DetailPenerimaan::class, 'id_penerimaan', 'id_penerimaan'); }
    public function stokBatch() { return $this->hasMany(StokBatchFifo::class, 'id_penerimaan', 'id_penerimaan'); }
}