<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Super Admin',
            'email' => 'admin@scm.com',
            'password' => Hash::make('admin123'),
            'role' => 'Admin',
            'jabatan' => 'Super Administrator',
            'status_user' => 'Aktif',
        ]);
    }
}
