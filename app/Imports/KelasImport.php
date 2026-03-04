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
            // Trim data untuk menghindari masalah whitespace
            $namaKelas = trim($row['nama_kelas']);
            $tahunAjaran = trim($row['tahun_ajaran']);

            // Cek duplikasi agar tidak double
            $exists = Kelas::where('nama_kelas', $namaKelas)
                ->where('tahun_ajaran', $tahunAjaran)
                ->exists();

            if ($exists) {
                return null;
            }

            return new Kelas([
                'nama_kelas' => $namaKelas,
                'tahun_ajaran' => $tahunAjaran,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_kelas' => 'required|max:50',
            '*.tahun_ajaran' => 'required|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nama_kelas.required' => 'Nama kelas harus diisi pada Excel',
            '*.tahun_ajaran.required' => 'Tahun ajaran harus diisi pada Excel',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}