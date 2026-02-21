<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanProyek extends Model
{
    use HasFactory;

    protected $table = 'penugasan_proyek';
    protected $primaryKey = 'id_penugasan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_penugasan',
        'id_user',
        'id_proyek',
        'peran_proyek',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_penugasan'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }
}