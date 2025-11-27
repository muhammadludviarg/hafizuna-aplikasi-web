<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $skippedCount = 0;

    public function model(array $row)
    {
        try {
            // Log data yang diproses
            Log::info('Processing row:', $row);

            // Validasi field tidak kosong
            if (empty($row['nama_siswa']) || empty($row['kode_siswa']) || empty($row['nama_kelas'])) {
                $this->skippedCount++;
                Log::warning('Skipping row - empty required fields', $row);
                return null;
            }

            // Cari kelas berdasarkan nama
            $kelas = Kelas::where('nama_kelas', trim($row['nama_kelas']))->first();
            
            if (!$kelas) {
                $this->skippedCount++;
                Log::warning('Skipping row - kelas not found: ' . $row['nama_kelas']);
                return null;
            }

            // Cek apakah kode siswa sudah ada
            $existingSiswa = Siswa::where('kode_siswa', trim($row['kode_siswa']))->first();
            if ($existingSiswa) {
                $this->skippedCount++;
                Log::warning('Skipping row - kode_siswa already exists: ' . $row['kode_siswa']);
                return null;
            }

            // Cari orang tua berdasarkan NAMA (bukan email)
            $id_ortu = null;
            if (!empty($row['nama_ortu'])) {
                // Cari di tabel akun berdasarkan nama_lengkap, lalu JOIN ke orang_tua
                $akun_ortu = User::where('nama_lengkap', trim($row['nama_ortu']))
                    ->whereHas('orang_tua') // Pastikan punya relasi orang_tua
                    ->with('orang_tua')
                    ->first();
                
                if ($akun_ortu && $akun_ortu->orang_tua) {
                    $id_ortu = $akun_ortu->orang_tua->id_ortu;
                    Log::info('Orang tua found: ' . $row['nama_ortu'] . ' (ID Akun: ' . $akun_ortu->id_akun . ', ID Ortu: ' . $id_ortu . ')');
                } else {
                    Log::warning('Orang tua not found for name: ' . $row['nama_ortu']);
                    // Tidak skip, tetap import tapi id_ortu null
                }
            }

            $this->importedCount++;
            
            // Buat siswa
            $siswa = new Siswa([
                'nama_siswa' => trim($row['nama_siswa']),
                'kode_siswa' => trim($row['kode_siswa']),
                'id_kelas'   => $kelas->id_kelas,
                'id_ortu'    => $id_ortu, // Bisa null kalau ortu tidak ditemukan
            ]);

            Log::info('Siswa created successfully', [
                'nama' => $siswa->nama_siswa,
                'kode' => $siswa->kode_siswa,
                'id_ortu' => $id_ortu
            ]);

            return $siswa;

        } catch (\Exception $e) {
            $this->skippedCount++;
            Log::error('Error importing row: ' . $e->getMessage(), [
                'row' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:100',
            'kode_siswa' => 'required|string|max:20',
            'nama_kelas' => 'required|string',
            'nama_ortu'  => 'nullable|string', // Ganti dari email_ortu jadi nama_ortu
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_siswa.required' => 'Nama siswa harus diisi',
            'kode_siswa.required' => 'Kode siswa harus diisi',
            'nama_kelas.required' => 'Nama kelas harus diisi',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
}