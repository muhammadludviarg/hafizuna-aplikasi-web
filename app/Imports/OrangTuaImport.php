<?php

namespace App\Imports;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // <--- 1. TAMBAHKAN INI

class OrangTuaImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows // <--- 2. TAMBAHKAN INI
{
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            $email = trim($row['email']);
            $nama = $row['nama_lengkap'];
            $noHp = isset($row['no_hp']) ? $row['no_hp'] : null;

            // 1. Cek apakah akun dengan email ini sudah ada?
            $akun = User::where('email', $email)->first();

            if (!$akun) {
                // Skenario A: Akun BELUM ADA -> Buat Baru
                $akun = User::create([
                    'nama_lengkap' => $nama,
                    'email' => $email,
                    'sandi_hash' => bcrypt('password123'),
                    'status' => 1,
                ]);
                Log::info("Import Ortu: Membuat akun baru untuk {$email}");
            } else {
                // Skenario B: Akun SUDAH ADA -> Gunakan yang lama
                if ($akun->nama_lengkap !== $nama) {
                    $akun->update(['nama_lengkap' => $nama]);
                }
                Log::info("Import Ortu: Menggunakan akun lama untuk {$email}");
            }

            // 2. Cek apakah data Orang Tua sudah ada untuk akun ini?
            $ortuExist = OrangTua::where('id_akun', $akun->id_akun)->exists();

            if ($ortuExist) {
                DB::commit();
                return null; // Skip jika sudah ada
            }

            // 3. Buat Data Orang Tua
            $ortu = OrangTua::create([
                'id_akun' => $akun->id_akun,
                'no_hp' => $noHp,
            ]);

            DB::commit();
            return $ortu;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Import Row: " . json_encode($row) . " | Error: " . $e->getMessage());
            // Kita biarkan error ini agar user tau ada data yang salah format (bukan kosong)
            throw new \Exception("Gagal pada baris {$row['nama_lengkap']}: " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email',
            'no_hp' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}