<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'email',
        'password',
        'ROLE',
        'jabatan',
        'status_user',
        'can_manage_master',
    ];

    protected $hidden = [
        'password',
    ];

   protected static function boot()
{
    parent::boot();

    static::creating(function ($user) {
        if (!$user->id_user) {
            // 1. Tentukan Prefix berdasarkan ROLE
            $prefix = match ($user->ROLE) {
                'Admin'           => 'ADM',
                'Tim Pengadaan'   => 'PGD',
                'Tim Pelaksanaan' => 'PLK',
                'Logistik'        => 'LOG',
                'Top Manajemen'   => 'TOP',
                default           => 'USR',
            };

            // 2. Cari nomor terakhir yang memiliki prefix tersebut
            $last = DB::table('users')
                ->where('id_user', 'LIKE', $prefix . '%')
                ->orderBy('id_user', 'desc')
                ->first();

            if ($last) {
                // Ambil 3 angka terakhir dan tambah 1
                $number = intval(substr($last->id_user, -3)) + 1;
                $user->id_user = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
            } else {
                // Jika belum ada user dengan role tersebut
                $user->id_user = $prefix . '001';
            }
        }
    });
}
}
