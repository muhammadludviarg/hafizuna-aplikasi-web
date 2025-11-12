<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guru;
use App\Models\OrangTua;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\SiswaKelompok;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data dummy untuk Guru, Siswa, dan Kelompok...');

        // 1. Bersihkan tabel terkait (hati-hati di produksi!)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SiswaKelompok::truncate();
        Siswa::truncate();
        OrangTua::truncate();
        Kelompok::truncate();
        Kelas::truncate();
        Guru::truncate();
        User::truncate();
        // Jangan hapus 'akun' jika sudah ada admin
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // 2. Buat 1 Akun Guru
        $akunGuru = User::create([
            'email' => 'guru.dummy@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Ustadz Ahmad Fauzi',
            'status' => true,
        ]);
        $guru = Guru::create([
            'id_akun' => $akunGuru->id_akun,
            'no_hp' => '08123456789'
        ]);

        // 3. Buat 1 Akun Ortu (MENGGUNAKAN EMAIL ASLI ANDA)
        $akunOrtu = User::create([
            'email' => 'muhammadludvi468@gmail.com', // <-- EMAIL ASLI ANDA
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Bapak Ludvi (Wali Murid)',
            'status' => true,
        ]);
        $ortu = OrangTua::create([
            'id_akun' => $akunOrtu->id_akun,
            'no_hp' => '081234567890'
        ]);

        // 4. Buat 1 Kelas
        $kelas = Kelas::create([
            'nama_kelas' => '5 Firdaus',
            'tahun_ajaran' => '2024'
        ]);

        // 5. Buat 1 Kelompok (diajar oleh guru di atas, di kelas di atas)
        $kelompok = Kelompok::create([
            'tahun_ajaran' => '2024/2025',
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);

        // 6. Buat 3 Siswa (anak dari ortu di atas, di kelas di atas)
        $siswa1 = Siswa::create([
            'nama_siswa' => 'Muhammad Ayub',
            'kode_siswa' => 'S-001',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);
        $siswa2 = Siswa::create([
            'nama_siswa' => 'Fatimah Azzahra',
            'kode_siswa' => 'S-002',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);
        $siswa3 = Siswa::create([
            'nama_siswa' => 'Aisyah Nur',
            'kode_siswa' => 'S-003',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);

        // 7. Masukkan 3 Siswa itu ke Kelompok
        SiswaKelompok::create([
            'id_siswa' => $siswa1->id_siswa,
            'id_kelompok' => $kelompok->id_kelompok,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(),
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa2->id_siswa,
            'id_kelompok' => $kelompok->id_kelompok,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(),
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa3->id_siswa,
            'id_kelompok' => $kelompok->id_kelompok,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(),
        ]);

        $this->command->info('Data dummy berhasil dibuat.');
        $this->command->info('Akun Guru: guru.dummy@hafizuna.com / password');
        $this->command->info('Akun Ortu: ortu.dummy@hafizuna.com / password');
    }
}