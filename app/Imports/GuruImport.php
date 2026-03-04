<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class GuruImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            $email = trim($row['email']);
            $nama = trim($row['nama_lengkap']);

            // 1. Cek apakah email sudah terdaftar di tabel akun (Bisa jadi dia Admin atau Ortu)
            $akun = User::where('email', $email)->first();

            if (!$akun) {
                // Jika akun BELUM ADA sama sekali, buatkan akun baru
                $akun = User::create([
                    'nama_lengkap' => $nama,
                    'email' => $email,
                    'sandi_hash' => bcrypt('password123'), // Password default
                    'status' => 1,
                ]);
            } else {
                // Jika akun SUDAH ADA, gunakan akun lama tersebut 
                // Opsional: Update namanya jika ada perubahan ketikan di Excel
                if ($akun->nama_lengkap !== $nama) {
                    $akun->update(['nama_lengkap' => $nama]);
                }
            }

            // 2. Cek apakah akun ini SUDAH tercatat sebagai Guru
            $guruExists = Guru::where('id_akun', $akun->id_akun)->exists();

            if ($guruExists) {
                // Jika sudah jadi Guru, lewati saja agar tidak double insert
                DB::commit();
                return null;
            }

            // 3. Tambahkan akun tersebut ke dalam tabel Guru
            $guru = Guru::create([
                'id_akun' => $akun->id_akun,
                'no_hp' => $row['no_hp'] ?? null,
            ]);

            DB::commit();
            return $guru;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal memproses baris '{$row['nama_lengkap']}': " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_lengkap' => 'required|max:100',
            '*.email' => 'required|email',
            '*.no_hp' => 'nullable|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nama_lengkap.required' => 'Nama lengkap harus diisi pada Excel',
            '*.email.required' => 'Email harus diisi pada Excel',
            '*.email.email' => 'Format email tidak valid pada Excel',
            '*.no_hp.max' => 'No HP maksimal 20 karakter',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}