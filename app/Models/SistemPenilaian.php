<?php
// app/Models/SistemPenilaian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemPenilaian extends Model
{
    use HasFactory;

    protected $table = 'sistem_penilaian';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'aspek',
        'grade',
        'proporsi_kesalahan_min',
        'proporsi_kesalahan_max',
    ];

    protected $casts = [
        'proporsi_kesalahan_min' => 'float',
        'proporsi_kesalahan_max' => 'float',
    ];

    /**
     * Dapatkan grade berdasarkan proporsi kesalahan
     */
    public static function getGrade(string $aspek, float $proporsiKesalahan): ?self
    {
        return self::where('aspek', $aspek)
            ->where('proporsi_kesalahan_min', '<=', $proporsiKesalahan)
            ->where('proporsi_kesalahan_max', '>=', $proporsiKesalahan)
            ->first();
    }

    /**
     * Hitung nilai numerik sederhana
     * Formula: Nilai = 100 - Proporsi Kesalahan
     */
    public static function hitungNilaiNumerik(float $proporsiKesalahan): int
    {
        $nilai = 100 - $proporsiKesalahan;
        
        // Pastikan nilai tidak negatif dan tidak lebih dari 100
        $nilai = max(0, min(100, $nilai));
        
        return (int) round($nilai);
    }

    /**
     * Hitung nilai akhir untuk 3 aspek sekaligus
     */
    public static function hitungNilaiAkhir(
        float $proporsiTajwid,
        float $proporsiMakhroj,
        float $proporsiKelancaran
    ): array {
        // Hitung nilai numerik (100 - proporsi)
        $nilaiTajwid = self::hitungNilaiNumerik($proporsiTajwid);
        $nilaiMakhroj = self::hitungNilaiNumerik($proporsiMakhroj);
        $nilaiKelancaran = self::hitungNilaiNumerik($proporsiKelancaran);
        
        // Dapatkan grade berdasarkan proporsi kesalahan
        $gradeTajwid = self::getGrade('tajwid', $proporsiTajwid)?->grade ?? 'C';
        $gradeMakhroj = self::getGrade('makhroj', $proporsiMakhroj)?->grade ?? 'C';
        $gradeKelancaran = self::getGrade('kelancaran', $proporsiKelancaran)?->grade ?? 'C';
        
        // Nilai rata-rata
        $nilaiRata = round(($nilaiTajwid + $nilaiMakhroj + $nilaiKelancaran) / 3, 2);
        
        return [
            'nilai_tajwid' => $nilaiTajwid,
            'nilai_makhroj' => $nilaiMakhroj,
            'nilai_kelancaran' => $nilaiKelancaran,
            'grade_tajwid' => $gradeTajwid,
            'grade_makhroj' => $gradeMakhroj,
            'grade_kelancaran' => $gradeKelancaran,
            'nilai_rata' => $nilaiRata,
        ];
    }
}