<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('sistem_penilaian')->truncate();

        // Data default untuk semua aspek
        // Formula: Nilai = 100 - Proporsi Kesalahan
        
        $data = [
            // KELANCARAN
            ['aspek' => 'kelancaran', 'grade' => 'A', 'proporsi_min' => 0, 'proporsi_max' => 5],
            ['aspek' => 'kelancaran', 'grade' => 'B', 'proporsi_min' => 5, 'proporsi_max' => 10],
            ['aspek' => 'kelancaran', 'grade' => 'C', 'proporsi_min' => 10, 'proporsi_max' => 100],
            
            // TAJWID
            ['aspek' => 'tajwid', 'grade' => 'A', 'proporsi_min' => 0, 'proporsi_max' => 3],
            ['aspek' => 'tajwid', 'grade' => 'B', 'proporsi_min' => 3, 'proporsi_max' => 7],
            ['aspek' => 'tajwid', 'grade' => 'C', 'proporsi_min' => 7, 'proporsi_max' => 100],
            
            // MAKHROJ
            ['aspek' => 'makhroj', 'grade' => 'A', 'proporsi_min' => 0, 'proporsi_max' => 3],
            ['aspek' => 'makhroj', 'grade' => 'B', 'proporsi_min' => 3, 'proporsi_max' => 7],
            ['aspek' => 'makhroj', 'grade' => 'C', 'proporsi_min' => 7, 'proporsi_max' => 100],
        ];

        foreach ($data as $item) {
            DB::table('sistem_penilaian')->insert([
                'aspek' => $item['aspek'],
                'grade' => $item['grade'],
                'proporsi_kesalahan_min' => $item['proporsi_min'],
                'proporsi_kesalahan_max' => $item['proporsi_max'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Berhasil menambahkan 9 data default sistem penilaian!');
    }
}