<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermintaanProyek extends Model
{
    protected $table = 'permintaan_proyek';
    protected $primaryKey = 'id_permintaan';
    public $incrementing = false; // Karena ID string
    protected $keyType = 'string';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_permintaan)) {
                $last = DB::table('permintaan_proyek')
                    ->orderBy('id_permintaan', 'desc')
                    ->first();

                if ($last) {
                    $number = intval(substr($last->id_permintaan, 3)) + 1;
                    $model->id_permintaan = 'REQ' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_permintaan = 'REQ0001';
                }
            }
        });
    }

    public function detailPermintaan() {
        return $this->hasMany(DetailPermintaanProyek::class, 'id_permintaan', 'id_permintaan');
    }

    public function proyek() {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}