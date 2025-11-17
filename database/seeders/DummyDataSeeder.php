<?php

namespace Database\Seeders;

use Illuminate;
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
        User::where('email', 'like', 'ortu%@hafizuna.com')->delete(); // Hapus semua ortu dummy
        User::where('email', 'muhammadludvi468@gmail.com')->delete(); // Hapus ortu utama

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

        // 6. Buat 3 Kelompok (Masing-masing 1 Guru)
        $this->command->info('Membuat 3 data dummy Kelompok...');
        $kelompokA = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru1->id_guru, // Ditangani Guru 1
            'nama_kelompok' => '5 Firdaus A',
        ]);
        $kelompokB = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru2->id_guru, // Ditangani Guru 2
            'nama_kelompok' => '5 Firdaus B',
        ]);
        $kelompokC = Kelompok::create([
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru3->id_guru, // Ditangani Guru 3
            'nama_kelompok' => '5 Firdaus C',
        ]);

        // 7. Buat 4 Akun Ortu
        $this->command->info('Membuat 4 data dummy Ortu...');

        // Ortu 1 (Akun Anda) - 2 Anak
        $akunOrtu1 = User::create([
            'email' => 'muhammadludvi468@gmail.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Bapak Ludvi (Wali Murid)',
            'status' => true,
        ]);
        $ortu1 = OrangTua::create([
            'id_akun' => $akunOrtu1->id_akun,
            'no_hp' => '081234567899'
        ]);

        // Ortu 2 (Dummy) - 2 Anak
        $akunOrtu2 = User::create([
            'email' => 'ortu2@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Ibu Aisyah (Wali Murid)',
            'status' => true,
        ]);
        $ortu2 = OrangTua::create([
            'id_akun' => $akunOrtu2->id_akun,
            'no_hp' => '081200000002'
        ]);

        // Ortu 3 (Dummy) - 1 Anak
        $akunOrtu3 = User::create([
            'email' => 'ortu3@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Bapak Ibrahim (Wali Murid)',
            'status' => true,
        ]);
        $ortu3 = OrangTua::create([
            'id_akun' => $akunOrtu3->id_akun,
            'no_hp' => '081200000003'
        ]);

        // Ortu 4 (Dummy) - 2 Anak
        $akunOrtu4 = User::create([
            'email' => 'ortu4@hafizuna.com',
            'sandi_hash' => Hash::make('password'),
            'nama_lengkap' => 'Bapak Sulaiman (Wali Murid)',
            'status' => true,
        ]);
        $ortu4 = OrangTua::create([
            'id_akun' => $akunOrtu4->id_akun,
            'no_hp' => '081200000004'
        ]);


        // 8. Buat 7 Siswa (untuk dibagi ke 3 kelompok & 4 ortu)
        $this->command->info('Membuat 7 data dummy Siswa...');

        // Siswa untuk Kelompok A (Wali: Ortu 1)
        $siswaA1 = Siswa::create([
            'nama_siswa' => 'Ahmad Yusuf',
            'kode_siswa' => 'S-001',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu1->id_ortu, // Anak Ortu 1
        ]);
        $siswaA2 = Siswa::create([
            'nama_siswa' => 'Ali Akbar',
            'kode_siswa' => 'S-002',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu1->id_ortu, // Anak Ortu 1
        ]);

        // Siswa untuk Kelompok B (Wali: Ortu 2 & 3)
        $siswaB1 = Siswa::create([
            'nama_siswa' => 'Fatimah Azzahra',
            'kode_siswa' => 'S-003',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu2->id_ortu, // Anak Ortu 2
        ]);
        $siswaB2 = Siswa::create([
            'nama_siswa' => 'Zainab Al-Ghazali',
            'kode_siswa' => 'S-004',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu2->id_ortu, // Anak Ortu 2
        ]);
        $siswaB3 = Siswa::create([
            'nama_siswa' => 'Khadijah Al-Kubra',
            'kode_siswa' => 'S-005',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu3->id_ortu, // Anak Ortu 3
        ]);

        // Siswa untuk Kelompok C (Wali: Ortu 4)
        $siswaC1 = Siswa::create([
            'nama_siswa' => 'Umar Bin Khattab',
            'kode_siswa' => 'S-006',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu4->id_ortu, // Anak Ortu 4
        ]);
        $siswaC2 = Siswa::create([
            'nama_siswa' => 'Utsman Bin Affan',
            'kode_siswa' => 'S-007',
            'id_kelas' => $kelas->id_kelas,
            'id_ortu' => $ortu4->id_ortu, // Anak Ortu 4
        ]);

        // 9. Masukkan Siswa ke Kelompok
        $this->command->info('Memasukkan siswa ke kelompok...');

        // Kelompok A (2 Siswa)
        SiswaKelompok::create(['id_siswa' => $siswaA1->id_siswa, 'id_kelompok' => $kelompokA->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);
        SiswaKelompok::create(['id_siswa' => $siswaA2->id_siswa, 'id_kelompok' => $kelompokA->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);

        // Kelompok B (3 Siswa)
        SiswaKelompok::create(['id_siswa' => $siswaB1->id_siswa, 'id_kelompok' => $kelompokB->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);
        SiswaKelompok::create(['id_siswa' => $siswaB2->id_siswa, 'id_kelompok' => $kelompokB->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);
        SiswaKelompok::create(['id_siswa' => $siswaB3->id_siswa, 'id_kelompok' => $kelompokB->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);

        // Kelompok C (2 Siswa)
        SiswaKelompok::create(['id_siswa' => $siswaC1->id_siswa, 'id_kelompok' => $kelompokC->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);
        SiswaKelompok::create(['id_siswa' => $siswaC2->id_siswa, 'id_kelompok' => $kelompokC->id_kelompok, 'tgl_mulai' => now(), 'tgl_selesai' => now()->addYear()]);

        $this->command->info('Data dummy berhasil dibuat.');
        $this->command->info('---------------------------------');
        $this->command->info('Akun Guru:');
        $this->command->info(' - guru1@hafizuna.com (Kelompok A)');
        $this->command->info(' - guru2@hafizuna.com (Kelompok B)');
        $this->command->info(' - guru3@hafizuna.com (Kelompok C)');
        $this->command->info('---------------------------------');
        $this->command->info('Akun Ortu:');
        $this->command->info(' - muhammadludvi468@gmail.com (2 anak)');
        $this->command->info(' - ortu2@hafizuna.com (2 anak)');
        $this->command->info(' - ortu3@hafizuna.com (1 anak)');
        $this->command->info(' - ortu4@hafizuna.com (2 anak)');
        $this->command->info('---------------------------------');
        $this->command->info('Password untuk semua akun: password');
    }
}