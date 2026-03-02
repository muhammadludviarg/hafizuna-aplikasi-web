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
        // SILAKAN GANTI DATA DI BAWAH INI 
        $admins = [
            [
                'nama'  => 'Lestiawati, M.Psi', 
                'email' => 'lestiawati@sdialazhar27.sch.id'
            ],   
            [
                'nama'  => 'Nabila, S.E', 
                'email' => 'nabila@sdialazhar27.sch.id'
            ],
            [
                'nama'  => 'Taufik Hermawan', 
                'email' => 'taufik.hermawan@sdialazhar27.sch.id'
            ],
            [
                'nama'  => 'Ferra Widiyana, S.Kom', 
                'email' => 'ferrawidiyana@sdialazhar27.sch.id'
            ],
            [
                'nama'  => 'Astrie Lestari, S.Pd', 
                'email' => 'astrielestari@sdialazhar27.sch.id'
            ],
            [
                'nama'  => 'Admin Dummy 1', 
                'email' => 'admin.dummy1@sdialazhar27.sch.id'
            ]
        ];

        foreach ($admins as $data) {
            // 1. Buat/Cari Akun
            $user = User::firstOrCreate(
                ['email' => $data['email']], // Cek berdasarkan email
                [
                    'nama_lengkap' => $data['nama'],
                    'sandi_hash'   => Hash::make('password123'), // Password default
                    'status'       => 1,
                ]
            );

            // 2. Hubungkan ke Tabel Admin
            Admin::firstOrCreate(
                ['id_akun' => $user->id_akun], // Cek berdasarkan id_akun
                [
                    // Jika tabel admin Anda butuh kolom no_hp, Anda bisa menambahkannya di sini:
                    // 'no_hp' => $data['no_hp'] 
                ]
            );
        }
    }
}