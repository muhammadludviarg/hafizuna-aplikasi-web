<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this -> call([
            // 1. Seed data master/statis terlebih dahulu
            QuranSeeder::class,
            SistemPenilaianSeeder::class,
            
            // 2. Buat akun admin SEBELUM data dummy
            AdminUserSeeder::class, 

            // 3. Seed data dummy transaksional
            DummyDataSeeder::class,
        ]);
    }
}