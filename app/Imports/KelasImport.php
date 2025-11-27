<?php

namespace App\Imports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        try {
            return new Kelas([
                'nama_kelas'   => $row['nama_kelas'],
                'tahun_ajaran' => $row['tahun_ajaran'],
            ]);
        } catch (\Exception $e) {
            return null; // Skip jika error
        }
    }

    public function rules(): array
    {
        return [
            'nama_kelas'   => 'required|string|max:50',
            'tahun_ajaran' => 'required|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kelas.required'   => 'Nama kelas harus diisi',
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}