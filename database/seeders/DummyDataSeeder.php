<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Guru;
use App\Models\OrangTua;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\SiswaKelompok;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data dummy untuk Guru, Siswa, dan Kelompok...');

        // 1. Bersihkan tabel terkait
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SiswaKelompok::truncate();
        Siswa::truncate();
        OrangTua::truncate();
        Kelompok::truncate();
        Kelas::truncate();
        Guru::truncate();
        
        // Hapus akun guru & ortu sebelumnya
        User::where('email', 'like', 'guru%@hafizuna.com')->delete();
        User::where('email', 'muhammadludvi468@gmail.com')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Membuat 3 data dummy Guru...');
        $tahunAjaran = '2024/2025';

        // 2. Buat Guru 1
        $akunGuru1 = User::create([
            'email' => 'guru1@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Ustadz Budi Santoso',
            'status' => true,
        ]);
        $guru1 = Guru::create([
            'id_akun' => $akunGuru1->id_akun,
            'no_hp' => '081234567890'
        ]);

        // 3. Buat Guru 2
        $akunGuru2 = User::create([
            'email' => 'guru2@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Ustadzah Citra Lestari',
            'status' => true,
        ]);
        $guru2 = Guru::create([
            'id_akun' => $akunGuru2->id_akun,
            'no_hp' => '081234567891'
        ]);

        // 4. Buat Guru 3
        $akunGuru3 = User::create([
            'email' => 'guru3@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Ustadz Doni Saputra',
            'status' => true,
        ]);
        $guru3 = Guru::create([
            'id_akun' => $akunGuru3->id_akun,
            'no_hp' => '081234567892'
        ]);

        // 5. Buat 1 Kelas
        $this->command->info('Membuat 1 data dummy Kelas...');
        $kelas = Kelas::create([
            'nama_kelas' => '5 Firdaus',
            'tahun_ajaran' => $tahunAjaran,
        ]);

        // 6. Buat 3 Kelompok (WAJIB DIBUAT SEBELUM SISWA)
        $this->command->info('Membuat 3 data dummy Kelompok...');
        $kelompokA = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru1->id_guru,
            'nama_kelompok' => '5 Firdaus A',
        ]);
        $kelompokB = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru2->id_guru,
            'nama_kelompok' => '5 Firdaus B',
        ]);
        $kelompokC = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru3->id_guru,
            'nama_kelompok' => '5 Firdaus C',
        ]);

        // 7. Buat 1 Akun Ortu (Email Asli Anda)
        $this->command->info('Membuat 1 data dummy Ortu...');
        $akunOrtu = User::create([
            'email' => 'muhammadludvi468@gmail.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Bapak Ludvi (Wali Murid)',
            'status' => true,
        ]);
        $ortu = OrangTua::create([
            'id_akun' => $akunOrtu->id_akun,
            'no_hp' => '081234567899'
        ]);

        // 8. Buat 3 Siswa
        $this->command->info('Membuat 3 data dummy Siswa...');
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
        $siswa4 = Siswa::create([
            'nama_siswa' => 'Aisyah muhammad',
            'kode_siswa' => 'S-004',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);
        $siswa5 = Siswa::create([
            'nama_siswa' => 'Aisyah muhammad abc',
            'kode_siswa' => 'S-005',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);
        $siswa6 = Siswa::create([
            'nama_siswa' => 'Muhammad abc',
            'kode_siswa' => 'S-006',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu->id_ortu,
        ]);

        // 9. Masukkan Siswa ke Kelompok
        $this->command->info('Memasukkan siswa ke kelompok...');
        SiswaKelompok::create([
            'id_siswa' => $siswa1->id_siswa,
            'id_kelompok' => $kelompokA->id_kelompok, // Siswa 1 ke Kelompok A
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa2->id_siswa,
            'id_kelompok' => $kelompokB->id_kelompok, // Siswa 2 ke Kelompok B
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa3->id_siswa,
            'id_kelompok' => $kelompokC->id_kelompok, // Siswa 3 ke Kelompok C
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa4->id_siswa,
            'id_kelompok' => $kelompokA->id_kelompok, // Siswa 4 ke Kelompok A
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa5->id_siswa,
            'id_kelompok' => $kelompokC->id_kelompok, // Siswa 5 ke Kelompok B
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);
        SiswaKelompok::create([
            'id_siswa' => $siswa6->id_siswa,
            'id_kelompok' => $kelompokC->id_kelompok, // Siswa 6 ke Kelompok C
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addYear(), // <-- INI PERBAIKANNYA
        ]);

        $this->command->info('Data dummy berhasil dibuat.');
        $this->command->info('Akun Guru: guru1@hafizuna.com / password');
        $this->command->info('Akun Ortu: muhammadludvi468@gmail.com / password');
    }
}