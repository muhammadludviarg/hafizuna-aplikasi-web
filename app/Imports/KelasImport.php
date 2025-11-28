<?php

namespace App\Imports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KelasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        try {
            // Cek duplikasi agar tidak double
            $exists = Kelas::where('nama_kelas', $row['nama_kelas'])
                ->where('tahun_ajaran', $row['tahun_ajaran'])
                ->exists();

            if ($exists) {
                return null;
            }

            return new Kelas([
                'nama_kelas' => $row['nama_kelas'],
                'tahun_ajaran' => $row['tahun_ajaran'],
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'required|string|max:50',
            'tahun_ajaran' => 'required|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}