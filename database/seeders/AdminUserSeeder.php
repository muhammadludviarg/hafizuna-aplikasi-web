<?php

namespace Database\Seeders;

use App\Models\User; // Pastikan ini model untuk tabel 'akun'
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ciptakan Akun User
        $userAdmin = User::create([
            'nama_lengkap' => 'Administrator Hafizuna',
            'email' => 'admin@hafizuna.com',
            // Gunakan sandi_hash (kolom di tabel akun) untuk menyimpan password
            'sandi_hash' => Hash::make('password'), 
            'status' => true, // Aktif
            // 'email_verified_at' => now(), // <-- DIHAPUS, karena tidak ada di migrasi tabel 'akun'
        ]);

        // 2. Hubungkan User tersebut dengan Role Admin
        // Kolom foreign key di tabel 'admin' adalah 'id_akun'
        Admin::create([
            'id_akun' => $userAdmin->id_akun,
        ]);
        
        $this->command->info('Akun Admin telah berhasil dibuat:');
        $this->command->info('Email: admin@hafizuna.com');
        $this->command->info('Password: password');
    }
}