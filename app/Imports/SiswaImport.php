<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\OrangTua;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // 1. Wajib ada

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        try {
            $namaSiswa = trim($row['nama_siswa']);
            $kodeSiswa = trim($row['kode_siswa']);
            $namaKelas = trim($row['nama_kelas']);
            $namaOrtu = isset($row['nama_ortu']) ? trim($row['nama_ortu']) : null;

            // 1. Validasi Kelas (Penyebab utama data tidak masuk)
            $kelas = Kelas::where('nama_kelas', $namaKelas)->first();

            if (!$kelas) {
                // JIKA KELAS TIDAK KETEMU -> LEMPAR ERROR (Jangan return null/skip)
                throw new \Exception("Kelas '$namaKelas' tidak ditemukan di database. Pastikan penulisan sama persis dengan Data Master Kelas.");
            }

            // 2. Cari Orang Tua (Smart Search)
            $id_ortu = null;
            if ($namaOrtu) {
                // Cari akun orang tua berdasarkan nama
                $akunOrtu = User::where('nama_lengkap', $namaOrtu)->first();

                if ($akunOrtu) {
                    // Cari data orang tua yang terhubung ke akun tsb
                    $dataOrtu = OrangTua::where('id_akun', $akunOrtu->id_akun)->first();
                    if ($dataOrtu) {
                        $id_ortu = $dataOrtu->id_ortu;
                    }
                }
            }

            // 3. Cek Duplikasi Kode Siswa
            $exist = Siswa::where('kode_siswa', $kodeSiswa)->exists();
            if ($exist) {
                throw new \Exception("Kode Siswa '$kodeSiswa' sudah digunakan.");
            }

            // 4. Simpan Siswa
            return new Siswa([
                'nama_siswa' => $namaSiswa,
                'kode_siswa' => $kodeSiswa,
                'id_kelas' => $kelas->id_kelas,
                'id_ortu' => $id_ortu, // Bisa null jika ortu tidak ketemu (tidak error, tapi kosong)
            ]);

        } catch (\Exception $e) {
            // Tangkap error dan lempar ke Controller agar muncul di Pop-up Merah
            throw new \Exception("Gagal baris '$namaSiswa': " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:100',
            'kode_siswa' => 'required|string|max:20',
            'nama_kelas' => 'required|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'kode_siswa.required' => 'Kode siswa wajib diisi',
            'nama_kelas.required' => 'Nama kelas wajib diisi',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}