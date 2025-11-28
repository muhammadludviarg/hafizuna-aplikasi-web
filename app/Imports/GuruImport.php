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
            // Cek apakah email sudah ada
            $emailExists = User::where('email', $row['email'])->exists();
            if ($emailExists) {
                DB::rollBack();
                return null; // Skip row ini
            }

            // Buat akun dulu
            $akun = User::create([
                'nama_lengkap' => $row['nama_lengkap'],
                'email'        => $row['email'],
                'sandi_hash'   => bcrypt('password123'), // Password default
                'status'       => 1, // Aktif
            ]);

            // Buat guru
            $guru = Guru::create([
                'id_akun' => $akun->id_akun,
                'no_hp'   => $row['no_hp'],
            ]);
            
            DB::commit();
            
            return $guru;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return null; // Skip jika error
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email',
            'no_hp'        => 'required|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Format email tidak valid',
            'no_hp.required'        => 'No HP harus diisi',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}