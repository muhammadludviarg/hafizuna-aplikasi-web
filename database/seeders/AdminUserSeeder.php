<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            'admin1@hafizuna.com',
            'admin2@hafizuna.com',
            'admin3@hafizuna.com',
        ];

        foreach ($admins as $index => $email) {
            // 1. Buat/Cari Akun
            $user = User::firstOrCreate(
                ['email' => $email], // Cek berdasarkan email
                [
                    'nama_lengkap' => 'Administrator ' . ($index + 1),
                    'sandi_hash' => Hash::make('password123'), // Password Hash
                    'status' => 1,
                ]
            );

            // 2. Hubungkan ke Tabel Admin
            Admin::firstOrCreate([
                'id_akun' => $user->id_akun
            ]);
        }
    }
}