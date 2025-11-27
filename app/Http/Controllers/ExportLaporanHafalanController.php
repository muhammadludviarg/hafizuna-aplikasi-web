<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\Kelompok;
use App\Models\TargetHafalanKelompok;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use ArPHP\I18N\Arabic;

class ExportLaporanHafalanController extends Controller
{
    public function exportPdf($kelasId)
    {
        $kelas = Kelas::with('siswa')->find($kelasId);

        if (!$kelas) {
            return abort(404);
        }

        // Prepare data siswa
        $siswaDetail = $kelas->siswa->map(function ($siswa) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();

            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0
                ? round($sesiHafalan->avg('nilai_rata'), 2)
                : 0;

            $totalAyat = 0;
            foreach ($sesiHafalan as $sesi) {
                $totalAyat += ($sesi->ayat_selesai - $sesi->ayat_mulai + 1);
            }

            return [
                'nama_siswa' => $siswa->nama_siswa,
                'total_ayat' => $totalAyat,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
            ];
        })->sortByDesc('nilai_rata_rata')
            ->values()
            ->toArray();

        $data = [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Laporan Hafalan Per Kelas',
            'nama_kelas' => $kelas->nama_kelas,
            'tahun_ajaran' => $kelas->tahun_ajaran,
            'tanggal' => date('d/m/Y'),
            'jumlah_siswa' => count($siswaDetail),
            'siswa_data' => $siswaDetail,
        ];

        $pdf = Pdf::loadView('exports.laporan-hafalan-pdf', $data);

        return $pdf->download('Laporan-Hafalan-' . $kelas->nama_kelas . '-' . date('dmY') . '.pdf');
    }

    public function exportExcel($kelasId)
    {
        $kelas = Kelas::with('siswa')->find($kelasId);

        if (!$kelas) {
            return abort(404);
        }

        // Prepare data siswa
        $siswaDetail = $kelas->siswa->map(function ($siswa) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();

            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0
                ? round($sesiHafalan->avg('nilai_rata'), 2)
                : 0;

            $totalAyat = 0;
            foreach ($sesiHafalan as $sesi) {
                $totalAyat += ($sesi->ayat_selesai - $sesi->ayat_mulai + 1);
            }

            return [
                'nama_siswa' => $siswa->nama_siswa,
                'total_ayat' => $totalAyat,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
            ];
        })->sortByDesc('nilai_rata_rata')
            ->values()
            ->toArray();

        $csvContent = $this->generateCsv($kelas, $siswaDetail);

        $filename = 'Laporan-Hafalan-' . str_replace(' ', '-', $kelas->nama_kelas) . '-' . date('d-m-Y') . '.csv';

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    private function generateCsv($kelas, $siswaDetail)
    {
        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM untuk Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header info
        fputcsv($output, ['HAFIZUNA - SD Islam Al-Azhar 27'], ',');
        fputcsv($output, ['Laporan Hafalan Per Kelas'], ',');
        fputcsv($output, ['Kelas: ' . $kelas->nama_kelas . ' | Tahun Ajaran: ' . $kelas->tahun_ajaran], ',');
        fputcsv($output, ['Tanggal: ' . date('d/m/Y') . ' | Jumlah Siswa: ' . count($siswaDetail)], ',');
        fputcsv($output, [], ',');

        // Column headers
        fputcsv($output, ['No', 'Nama Siswa', 'Total Ayat', 'Jumlah Sesi', 'Nilai Rata-rata'], ',');

        // Data rows
        foreach ($siswaDetail as $index => $siswa) {
            fputcsv($output, [
                $index + 1,
                $siswa['nama_siswa'],
                $siswa['total_ayat'],
                $siswa['jumlah_sesi'],
                $siswa['nilai_rata_rata']
            ], ',');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function exportPdfSesi($siswaId, $surahId)
    {
        // 1. LOAD DATA SISWA + KELAS + KELOMPOK & GURUNYA
        $siswa = Siswa::with(['kelas', 'kelompok.guru'])->find($siswaId);
        $surah = Surah::find($surahId);

        if (!$siswa || !$surah)
            return abort(404);

        // 2. LOGIKA GURU (Dari Kelompok Siswa)
        // Ambil kelompok pertama siswa, lalu ambil gurunya
        $namaGuru = '-';
        $kelompokSiswa = $siswa->kelompok->first();

        if ($kelompokSiswa && $kelompokSiswa->guru) {
            $namaGuru = $kelompokSiswa->guru->nama_guru;
        }

        // 3. AMBIL SESI UNTUK SURAH INI
        $allSesi = SesiHafalan::where('id_siswa', $siswaId)
            ->where(function ($q) use ($surahId) {
                $q->where('id_surah_mulai', $surahId)
                    ->orWhere('id_surah_selesai', $surahId);
            })
            ->with(['koreksi.ayat'])
            ->orderBy('tanggal_setor', 'asc')
            ->get();

        if ($allSesi->isEmpty())
            return abort(404, 'Sesi tidak ditemukan');

        $latestSesi = $allSesi->last();

        // 4. PERBAIKI TEKS ARAB (Reshaping)
        // Walaupun dari API, teks harus di-reshape agar nyambung di PDF
        $arabic = new Arabic();
        $allKoreksiHistory = [];

        foreach ($allSesi as $index => $sesi) {
            $nomorSesi = $index + 1;
            foreach ($sesi->koreksi as $k) {
                // Perbaiki teks Arab
                $catatanFixed = $k->catatan ? $arabic->utf8Glyphs($k->catatan) : '-';

                $allKoreksiHistory[] = [
                    'lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')),
                    'sesi_ke' => $nomorSesi,
                    'jenis_kesalahan' => $k->kategori_kesalahan ?? '-',
                    'catatan' => $catatanFixed // Teks yang sudah diperbaiki
                ];
            }
        }

        $allKoreksiHistory = array_reverse($allKoreksiHistory);

        // Data Pelengkap
        $namaKelas = $siswa->kelas ? $siswa->kelas->nama_kelas : 'N/A';
        $nilaiRataRata = $latestSesi->nilai_rata ?? 0;

        // Grade
        $gradeTajwid = $this->getGradeDescription($latestSesi->skor_tajwid);
        $gradeKelancaran = $this->getGradeDescription($latestSesi->skor_kelancaran);
        $gradeMakhroj = $this->getGradeDescription($latestSesi->skor_makhroj);
        $gradeDesc = $this->getGradeDescription($nilaiRataRata);

        // Data Riwayat Nilai
        $riwayatSesiFormatted = $allSesi->sortByDesc('tanggal_setor')->map(function ($sesi) {
            return [
                'tanggal' => $sesi->tanggal_setor->format('d/m/Y'),
                'ayat' => $sesi->ayat_mulai . '-' . $sesi->ayat_selesai,
                'tajwid' => number_format($sesi->skor_tajwid, 1),
                'kelancaran' => number_format($sesi->skor_kelancaran, 1),
                'makhroj' => number_format($sesi->skor_makhroj, 1),
                'rata_rata' => number_format($sesi->nilai_rata, 2)
            ];
        })->values()->toArray();

        $pdf = Pdf::loadView('exports.sesi-setoran-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'judul' => 'Riwayat Koreksi Hafalan',
            'nama_siswa' => $siswa->nama_siswa,
            'nama_kelas' => $namaKelas,
            'nama_surah' => $surah->nama_surah,
            'ayat_mulai' => $latestSesi->ayat_mulai,
            'ayat_selesai' => $latestSesi->ayat_selesai,
            'nama_guru' => $namaGuru, // ✅ Guru Pembimbing dari Kelompok
            'tanggal_sesi' => Carbon::parse($latestSesi->tanggal_setor)->translatedFormat('l, d F Y'),
            'nilai_tajwid' => number_format($latestSesi->skor_tajwid, 1),
            'nilai_kelancaran' => number_format($latestSesi->skor_kelancaran, 1),
            'nilai_makhroj' => number_format($latestSesi->skor_makhroj, 1),
            'nilai_rata_rata' => number_format($nilaiRataRata, 2),
            'grade_tajwid' => $gradeTajwid,
            'grade_kelancaran' => $gradeKelancaran,
            'grade_makhroj' => $gradeMakhroj,
            'grade_desc' => $gradeDesc,
            'koreksi' => $allKoreksiHistory,
            'riwayat_sesi' => $riwayatSesiFormatted,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '.pdf');
    }

    private function generateCsvSiswa($siswa, $surahStats, $surahBelumDihafalkan)
    {
        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM untuk Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header info
        fputcsv($output, ['HAFIZUNA - SD Islam Al-Azhar 27'], ',');
        fputcsv($output, ['Laporan Hafalan Al-Qur\'an'], ',');
        fputcsv($output, ['Nama Siswa: ' . $siswa->nama_siswa], ',');
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')], ',');
        fputcsv($output, [], ',');

        // Section 1: Surah yang Sudah Dihafalkan
        fputcsv($output, ['Surah yang Sudah Dihafalkan'], ',');
        fputcsv($output, ['No', 'Nama Surah', 'Sesi', 'Tajwid', 'Kelancaran', 'Makhroj', 'Rata-rata'], ',');

        foreach ($surahStats as $index => $surah) {
            fputcsv($output, [
                $index + 1,
                $surah['nama_surah'],
                $surah['jumlah_sesi'],
                $surah['nilai_tajwid'],
                $surah['nilai_kelancaran'],
                $surah['nilai_makhroj'],
                $surah['nilai_rata']
            ], ',');
        }

        fputcsv($output, [], ',');

        // Section 2: Target Hafalan yang Belum Dihafalkan
        fputcsv($output, ['Target Hafalan yang Belum Dihafalkan'], ',');
        fputcsv($output, ['No', 'Nama Surah', 'Status', 'Progress'], ',');

        foreach ($surahBelumDihafalkan as $index => $surah) {
            fputcsv($output, [
                $index + 1,
                $surah['nama_surah'],
                $surah['status'],
                $surah['progress']
            ], ',');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function exportExcelSesi($siswaId, $surahId)
    {
        $siswa = Siswa::with('kelas')->find($siswaId);
        $surah = Surah::find($surahId);

        if (!$siswa || !$surah)
            return abort(404);

        $allSesi = SesiHafalan::where('id_siswa', $siswaId)
            ->where(function ($q) use ($surahId) {
                $q->where('id_surah_mulai', $surahId)
                    ->orWhere('id_surah_selesai', $surahId);
            })
            ->with(['guru', 'koreksi.ayat'])
            ->orderBy('tanggal_setor', 'asc') // Ascending untuk hitung urutan
            ->get();

        if ($allSesi->isEmpty())
            return abort(404, 'Sesi tidak ditemukan');

        $latestSesi = $allSesi->last();

        $namaKelas = $siswa->kelas ? $siswa->kelas->nama_kelas : 'N/A';
        $namaGuru = $latestSesi->guru ? $latestSesi->guru->nama_guru : '-';
        $nilaiRataRata = $latestSesi->nilai_rata ?? 0;

        // KUMPULKAN RIWAYAT KOREKSI UNTUK EXCEL
        $allKoreksiHistory = [];
        foreach ($allSesi as $index => $sesi) {
            $nomorSesi = $index + 1;
            foreach ($sesi->koreksi as $k) {
                // Di Excel TIDAK PERLU utf8Glyphs, karena Excel native support Arab
                $allKoreksiHistory[] = [
                    'lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')),
                    'sesi_ke' => $nomorSesi,
                    'tanggal' => $sesi->tanggal_setor->format('d/m/Y'),
                    'jenis_kesalahan' => $k->kategori_kesalahan ?? '-',
                    'catatan' => $k->catatan ?? '-'
                ];
            }
        }

        // Urutkan dari terbaru
        $allKoreksiHistory = array_reverse($allKoreksiHistory);

        // Deskripsi grade
        $gradeDesc = $this->getGradeDescription($nilaiRataRata);

        // Generate CSV
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM Header

        // Header Laporan
        fputcsv($output, ['HAFIZUNA - SD Islam Al-Azhar 27 Cibinong Bogor'], ',');
        fputcsv($output, ['Detail Riwayat Sesi Setoran Hafalan'], ',');
        fputcsv($output, [], ',');

        // Informasi Sesi (Ambil status terakhir)
        fputcsv($output, ['INFORMASI TERKINI'], ',');
        fputcsv($output, ['Siswa', $siswa->nama_siswa], ',');
        fputcsv($output, ['Kelas', $namaKelas], ',');
        fputcsv($output, ['Surah', $surah->nama_surah], ',');
        fputcsv($output, ['Guru', $namaGuru], ',');
        fputcsv($output, ['Tanggal Terakhir', $latestSesi->tanggal_setor->format('d/m/Y')], ',');
        fputcsv($output, [], ',');

        // Tabel Riwayat Koreksi
        if (count($allKoreksiHistory) > 0) {
            fputcsv($output, ['RIWAYAT CATATAN KOREKSI (SEMUA SESI)'], ',');
            fputcsv($output, ['No', 'Tanggal', 'Sesi Ke-', 'Lokasi', 'Jenis Kesalahan', 'Catatan'], ',');

            foreach ($allKoreksiHistory as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item['tanggal'],
                    $item['sesi_ke'], // ✅ Kolom Sesi Ke
                    $item['lokasi'],
                    $item['jenis_kesalahan'],
                    $item['catatan']
                ], ',');
            }
        } else {
            fputcsv($output, ['Tidak ada catatan koreksi untuk surah ini.'], ',');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $filename = 'Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '.csv';

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateCsvSesi($siswa, $surah, $namaKelas, $namaGuru, $ayatMulai, $ayatSelesai, $nilaiTajwid, $nilaiKelancaran, $nilaiMakhroj, $nilaiRataRata, $gradeDesc, $allKoreksiHistory, $sesiSurah)
    {
        $output = fopen('php://temp', 'r+');

        // 1. Add UTF-8 BOM agar Excel bisa baca karakter Arab dengan benar
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // 2. Header Laporan (Judul Sekolah)
        fputcsv($output, ['HAFIZUNA'], ',');
        fputcsv($output, ['SD Islam Al-Azhar 27 Cibinong Bogor'], ',');
        fputcsv($output, ['Detail Riwayat Hafalan Siswa'], ',');
        fputcsv($output, [], ','); // Baris kosong

        // 3. INFORMASI UTAMA (Header Data)
        fputcsv($output, ['INFORMASI TERKINI'], ',');
        fputcsv($output, ['Nama Siswa', $siswa->nama_siswa], ',');
        fputcsv($output, ['Kelas', $namaKelas], ',');
        fputcsv($output, ['Surah', $surah->nama_surah], ',');
        fputcsv($output, ['Guru Pembimbing Terakhir', $namaGuru], ',');
        fputcsv($output, ['Tanggal Update', date('d/m/Y')], ',');
        fputcsv($output, [], ',');

        // 4. PENILAIAN TERAKHIR (Ringkasan)
        fputcsv($output, ['NILAI TERAKHIR'], ',');
        fputcsv($output, ['Aspek', 'Nilai'], ',');
        fputcsv($output, ['Tajwid', $nilaiTajwid], ',');
        fputcsv($output, ['Kelancaran', $nilaiKelancaran], ',');
        fputcsv($output, ['Makhroj', $nilaiMakhroj], ',');
        fputcsv($output, ['RATA-RATA', $nilaiRataRata . ' (' . $gradeDesc . ')'], ',');
        fputcsv($output, [], ',');

        // 5. TABEL RIWAYAT KOREKSI (Bagian Penting)
        fputcsv($output, ['RIWAYAT CATATAN KOREKSI (DARI SEMUA SESI)'], ',');

        // Header Tabel Koreksi
        fputcsv($output, [
            'No',
            'Tanggal',
            'Sesi Ke-',       // ✅ Kolom baru sesuai permintaan
            'Lokasi Ayat',
            'Jenis Kesalahan',
            'Catatan (Lafadz)'
        ], ',');

        if (count($allKoreksiHistory) > 0) {
            foreach ($allKoreksiHistory as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item['tanggal'],           // Tanggal Sesi
                    $item['sesi_ke'],           // Sesi Ke-X
                    $item['lokasi'],            // Ayat berapa
                    $item['jenis_kesalahan'],   // Jenis
                    $item['catatan']            // Teks Arab/Catatan
                ], ',');
            }
        } else {
            fputcsv($output, ['Tidak ada catatan koreksi untuk surah ini.'], ',');
        }

        fputcsv($output, [], ',');

        // 6. RINGKASAN DATA NILAI PER SESI (Opsional, biar lengkap)
        fputcsv($output, ['REKAPITULASI NILAI PER PERTEMUAN'], ',');
        fputcsv($output, ['No', 'Tanggal', 'Ayat', 'Tajwid', 'Kelancaran', 'Makhroj', 'Rata-rata'], ',');

        foreach ($sesiSurah as $index => $sesi) {
            fputcsv($output, [
                $index + 1,
                $sesi->tanggal_setor->format('d/m/Y'),
                $sesi->ayat_mulai . '-' . $sesi->ayat_selesai,
                $sesi->skor_tajwid,
                $sesi->skor_kelancaran,
                $sesi->skor_makhroj,
                $sesi->nilai_rata
            ], ',');
        }

        // Finalisasi
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
    private function getGradeDescription($nilai)
    {
        if ($nilai >= 90) {
            return 'Sangat Baik';
        } elseif ($nilai >= 80) {
            return 'Baik';
        } elseif ($nilai >= 70) {
            return 'Cukup';
        } else {
            return 'Kurang';
        }
    }
}
