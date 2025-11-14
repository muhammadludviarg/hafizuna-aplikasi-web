<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Surah;
use App\Models\Ayat;

class QuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai proses Seeding data Al-Qur\'an...');
        
        // 1. Kosongkan tabel (opsional, tapi aman)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ayat::truncate();
        Surah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Ambil daftar Surah dari API
        $responseSurah = Http::get('https://api.quran.com/api/v4/chapters?language=id');
        if (!$responseSurah->successful()) {
            $this->command->error('Gagal mengambil daftar Surah.');
            return;
        }

        $daftarSurah = $responseSurah->json('chapters');
        $this->command->getOutput()->progressStart(count($daftarSurah));

        // 3. Loop setiap surah
        foreach ($daftarSurah as $surahData) {
            // Simpan ke tabel 'surah'
            $surah = Surah::create([
                'id_surah' => $surahData['id'], // Gunakan id dari API
                'nomor_surah' => $surahData['id'],
                'nama_surah' => $surahData['name_simple'], // Pastikan kolom 'nama_surah' cukup besar (misal varchar 100)
                'jumlah_ayat' => $surahData['verses_count'],
            ]);

            // 4. Ambil semua ayat untuk surah ini (versi teks Utsmani)
            $responseAyat = Http::get('https://api.quran.com/api/v4/quran/verses/uthmani', [
                'chapter_number' => $surah->nomor_surah
            ]);
            
            if (!$responseAyat->successful()) {
                $this->command->error("Gagal mengambil ayat untuk Surah {$surah->nama_surah}");
                continue;
            }

            // 5. Loop setiap ayat dan simpan
            foreach ($responseAyat->json('verses') as $ayatData) {
                $teksArab = $ayatData['text_uthmani'];
                $nomorAyat = explode(':', $ayatData['verse_key'])[1]; // Ambil nomor ayat dari "1:1"
                
                Ayat::create([
                    'id_surah' => $surah->id_surah,
                    'nomor_ayat' => $nomorAyat,
                    'teks_arab' => $teksArab,
                    //'terjemahan' => '', // API ini tidak menyertakan terjemahan di endpoint ini
                    'jumlah_kata' => count(explode(' ', $teksArab)), // Hitung jumlah kata
                ]);
            }

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Seeding data Al-Qur\'an selesai.');
    }
}