<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\Kelompok;
use App\Models\TargetHafalanKelompok;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function exportPdfSiswa($siswaId)
    {
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return abort(404);
        }

        // Ambil sesi hafalan siswa - surah yang sudah dihafalkan
        $sesiHafalan = SesiHafalan::where('id_siswa', $siswaId)
            ->with('surahMulai', 'surahSelesai')
            ->orderBy('tanggal_setor')
            ->get();

        // Format data surah yang sudah dihafalkan
        $surahDihafalkan = [];
        $surahIds = [];

        foreach ($sesiHafalan as $sesi) {
            $surahIds[] = $sesi->id_surah_mulai;
            if ($sesi->id_surah_mulai !== $sesi->id_surah_selesai) {
                $surahIds[] = $sesi->id_surah_selesai;
            }
        }

        $surahIds = array_unique($surahIds);

        // Ambil data unik per surah dengan statistik terbaru
        $surahStats = [];
        foreach ($surahIds as $surahId) {
            $sesiSurah = SesiHafalan::where('id_siswa', $siswaId)
                ->where(function ($q) use ($surahId) {
                    $q->where('id_surah_mulai', $surahId)
                        ->orWhere('id_surah_selesai', $surahId);
                })
                ->get();

            if ($sesiSurah->count() > 0) {
                $surah = Surah::find($surahId);
                $jumlahSesi = $sesiSurah->count();

                $surahStats[] = [
                    'no' => $surah->nomor_surah,
                    'nama_surah' => $surah->nama_surah,
                    'jumlah_sesi' => $jumlahSesi,
                    'nilai_tajwid' => round($sesiSurah->avg('skor_tajwid'), 2),
                    'nilai_kelancaran' => round($sesiSurah->avg('skor_kelancaran'), 2),
                    'nilai_makhroj' => round($sesiSurah->avg('skor_makhroj'), 2),
                    'nilai_rata' => round($sesiSurah->avg('nilai_rata'), 2),
                ];
            }
        }

        // Urutkan berdasarkan nomor surah
        usort($surahStats, function ($a, $b) {
            return $a['no'] - $b['no'];
        });

        $surahBelumDihafalkan = [];
        if ($siswa) {
            // Ambil semua kelompok siswa
            $siswaKelompok = $siswa->kelompok;

            if ($siswaKelompok->isNotEmpty()) {
                // Get target hafalan from all kelompok that this siswa belongs to
                $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
                $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

                foreach ($targetHafalan as $target) {
                    $surahAwal = $target->id_surah_awal;
                    $surahAkhir = $target->id_surah_akhir;

                    $listSurahTarget = range($surahAwal, $surahAkhir);

                    foreach ($listSurahTarget as $i) {
                        // Check if already hafal
                        $sudahDihafalkan = SesiHafalan::where('id_siswa', $siswaId)
                            ->where(function ($q) use ($i) {
                                $q->where('id_surah_mulai', $i)
                                    ->orWhere('id_surah_selesai', $i);
                            })
                            ->exists();

                        if (!$sudahDihafalkan) {
                            $surah = Surah::find($i);
                            if ($surah) {
                                $surahBelumDihafalkan[] = [
                                    'no' => $surah->nomor_surah,
                                    'nama_surah' => $surah->nama_surah,
                                    'status' => 'Belum Dimulai',
                                    'progress' => '0/' . $surah->jumlah_ayat . ' ayat',
                                ];
                            }
                        }
                    }
                }
            }
        }

        $data = [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Laporan Hafalan Al-Qur\'an',
            'nama_siswa' => $siswa->nama_siswa,
            'tanggal' => date('d/m/Y'),
            'surah_dihafalkan' => $surahStats,
            'surah_belum_dihafalkan' => $surahBelumDihafalkan,
        ];

        $pdf = Pdf::loadView('exports.laporan-hafalan-siswa-pdf', $data);

        return $pdf->download('Laporan-Hafalan-' . $siswa->nama_siswa . '-' . date('dmY') . '.pdf');
    }

    public function exportExcelSiswa($siswaId)
    {
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return abort(404);
        }

        // Ambil sesi hafalan siswa
        $sesiHafalan = SesiHafalan::where('id_siswa', $siswaId)
            ->with('surahMulai', 'surahSelesai')
            ->orderBy('tanggal_setor')
            ->get();

        // Format data surah yang sudah dihafalkan
        $surahDihafalkan = [];
        $surahIds = [];

        foreach ($sesiHafalan as $sesi) {
            $surahIds[] = $sesi->id_surah_mulai;
            if ($sesi->id_surah_mulai !== $sesi->id_surah_selesai) {
                $surahIds[] = $sesi->id_surah_selesai;
            }
        }

        $surahIds = array_unique($surahIds);

        // Ambil data unik per surah
        $surahStats = [];
        foreach ($surahIds as $surahId) {
            $sesiSurah = SesiHafalan::where('id_siswa', $siswaId)
                ->where(function ($q) use ($surahId) {
                    $q->where('id_surah_mulai', $surahId)
                        ->orWhere('id_surah_selesai', $surahId);
                })
                ->get();

            if ($sesiSurah->count() > 0) {
                $surah = Surah::find($surahId);
                $jumlahSesi = $sesiSurah->count();

                $surahStats[] = [
                    'no' => $surah->nomor_surah,
                    'nama_surah' => $surah->nama_surah,
                    'jumlah_sesi' => $jumlahSesi,
                    'nilai_tajwid' => round($sesiSurah->avg('skor_tajwid'), 2),
                    'nilai_kelancaran' => round($sesiSurah->avg('skor_kelancaran'), 2),
                    'nilai_makhroj' => round($sesiSurah->avg('skor_makhroj'), 2),
                    'nilai_rata' => round($sesiSurah->avg('nilai_rata'), 2),
                ];
            }
        }

        // Urutkan berdasarkan nomor surah
        usort($surahStats, function ($a, $b) {
            return $a['no'] - $b['no'];
        });

        $surahBelumDihafalkan = [];
        if ($siswa) {
            // Ambil semua kelompok siswa
            $siswaKelompok = $siswa->kelompok;

            if ($siswaKelompok->isNotEmpty()) {
                // Get target hafalan from all kelompok that this siswa belongs to
                $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
                $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

                foreach ($targetHafalan as $target) {
                    // Gunakan range() agar bisa membaca urutan mundur
                    $surahAwal = $target->id_surah_awal;
                    $surahAkhir = $target->id_surah_akhir;
                    $listSurahTarget = range($surahAwal, $surahAkhir);

                    foreach ($listSurahTarget as $i) {
                        // Check if already hafal
                        $sudahDihafalkan = SesiHafalan::where('id_siswa', $siswaId)
                            ->where(function ($q) use ($i) {
                                $q->where('id_surah_mulai', $i)
                                    ->orWhere('id_surah_selesai', $i);
                            })
                            ->exists();

                        if (!$sudahDihafalkan) {
                            $surah = Surah::find($i);
                            if ($surah) {
                                $surahBelumDihafalkan[] = [
                                    'no' => $surah->nomor_surah,
                                    'nama_surah' => $surah->nama_surah,
                                    'status' => 'Belum Dimulai',
                                    'progress' => '0/' . $surah->jumlah_ayat . ' ayat',
                                ];
                            }
                        }
                    }
                }
            }
        }

        $csvContent = $this->generateCsvSiswa($siswa, $surahStats, $surahBelumDihafalkan);

        $filename = 'Laporan-Hafalan-' . str_replace(' ', '-', $siswa->nama_siswa) . '-' . date('d-m-Y') . '.csv';

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
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

    public function exportPdfSesi($siswaId, $surahId)
    {
        $siswa = Siswa::find($siswaId);
        $surah = Surah::find($surahId);

        if (!$siswa || !$surah) {
            return abort(404);
        }

        // Get all sessions for this surah
        $sesiSurah = SesiHafalan::where('id_siswa', $siswaId)
            ->where(function ($q) use ($surahId) {
                $q->where('id_surah_mulai', $surahId)
                    ->orWhere('id_surah_selesai', $surahId);
            })
            ->with(['surahMulai', 'surahSelesai', 'guru', 'koreksi'])
            ->orderByDesc('tanggal_setor')
            ->get();

        if ($sesiSurah->isEmpty()) {
            return abort(404, 'Sesi tidak ditemukan');
        }

        // Get latest session for details
        $latestSesi = $sesiSurah->first();

        // Get siswa's kelas
        $kelas = $siswa->kelompok()->first();
        $namaKelas = $kelas ? $kelas->nama_kelas : 'N/A';
        $namaGuru = $latestSesi->guru->nama_guru ?? 'N/A';
        
        $nilaiTajwid = $latestSesi->skor_tajwid ?? 0;
        $nilaiKelancaran = $latestSesi->skor_kelancaran ?? 0;
        $nilaiMakhroj = $latestSesi->skor_makhroj ?? 0;
        $nilaiRataRata = $latestSesi->nilai_rata ?? (($nilaiTajwid + $nilaiKelancaran + $nilaiMakhroj) / 3);

        // Get latest session details
        $ayatMulai = $latestSesi->ayat_mulai;
        $ayatSelesai = $latestSesi->ayat_selesai;

        $koreksi = $latestSesi->koreksi->map(function ($k) {
            return [
                'lokasi' => 'Ayat ' . ($k->kata_ke ?? '?'),
                'jenis_kesalahan' => $k->jenis_kesalahan ?? '-',
                'catatan' => $k->catatan ?? '-'
            ];
        })->toArray();

        $riwayatSesi = $sesiSurah->map(function ($sesi) {
            $rataRata = $sesi->nilai_rata ?? (($sesi->skor_tajwid + $sesi->skor_kelancaran + $sesi->skor_makhroj) / 3);
            return [
                'tanggal' => \Carbon\Carbon::parse($sesi->tanggal_setor)->format('d/m/Y'),
                'ayat' => $sesi->ayat_mulai . '-' . $sesi->ayat_selesai,
                'tajwid' => number_format($sesi->skor_tajwid, 1),
                'kelancaran' => number_format($sesi->skor_kelancaran, 1),
                'makhroj' => number_format($sesi->skor_makhroj, 1),
                'rata_rata' => number_format($rataRata, 2)
            ];
        })->toArray();

        $gradeTajwid = $this->getGradeDescription($nilaiTajwid);
        $gradeKelancaran = $this->getGradeDescription($nilaiKelancaran);
        $gradeMakhroj = $this->getGradeDescription($nilaiMakhroj);
        $gradeDesc = $this->getGradeDescription($nilaiRataRata);

        $tanggalSesi = \Carbon\Carbon::parse($latestSesi->tanggal_setor)->format('l, d F Y');

        $pdf = Pdf::loadView('exports.sesi-setoran-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Detail Sesi Setoran Hafalan',
            'nama_siswa' => $siswa->nama_siswa,
            'nama_kelas' => $namaKelas,
            'nama_surah' => $surah->nama_surah,
            'ayat_mulai' => $ayatMulai,
            'ayat_selesai' => $ayatSelesai,
            'nama_guru' => $namaGuru,
            'tanggal_sesi' => $tanggalSesi,
            'nilai_tajwid' => number_format($nilaiTajwid, 1),
            'nilai_kelancaran' => number_format($nilaiKelancaran, 1),
            'nilai_makhroj' => number_format($nilaiMakhroj, 1),
            'nilai_rata_rata' => number_format($nilaiRataRata, 2),
            'grade_tajwid' => $gradeTajwid,
            'grade_kelancaran' => $gradeKelancaran,
            'grade_makhroj' => $gradeMakhroj,
            'grade_desc' => $gradeDesc,
            'koreksi' => $koreksi,
            'riwayat_sesi' => $riwayatSesi
        ])->setPaper('a4', 'portrait');

        $filename = 'Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '-' . str_replace(' ', '-', $surah->nama_surah) . '-' . date('d-m-Y') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcelSesi($siswaId, $surahId)
    {
        $siswa = Siswa::find($siswaId);
        $surah = Surah::find($surahId);

        if (!$siswa || !$surah) {
            return abort(404);
        }

        // Get all sessions for this surah
        $sesiSurah = SesiHafalan::where('id_siswa', $siswaId)
            ->where(function ($q) use ($surahId) {
                $q->where('id_surah_mulai', $surahId)
                    ->orWhere('id_surah_selesai', $surahId);
            })
            ->with(['surahMulai', 'surahSelesai', 'guru', 'koreksi'])
            ->orderByDesc('tanggal_setor')
            ->get();

        if ($sesiSurah->isEmpty()) {
            return abort(404, 'Sesi tidak ditemukan');
        }

        // Get latest session for details
        $latestSesi = $sesiSurah->first();

        // Get siswa's kelas
        $kelas = $siswa->kelompok()->first();
        $namaKelas = $kelas ? $kelas->nama_kelas : 'N/A';
        
        $namaGuru = $latestSesi->guru->nama_guru ?? 'N/A';
        
        // Calculate average scores
        $nilaiTajwid = round($sesiSurah->avg('skor_tajwid'), 2);
        $nilaiKelancaran = round($sesiSurah->avg('skor_kelancaran'), 2);
        $nilaiMakhroj = round($sesiSurah->avg('skor_makhroj'), 2);
        $nilaiRataRata = round($sesiSurah->avg('nilai_rata'), 2);

        // Get latest session details
        $ayatMulai = $latestSesi->ayat_mulai;
        $ayatSelesai = $latestSesi->ayat_selesai;

        $koreksi = $latestSesi->koreksi->map(function ($k) {
            $nomorAyat = $k->kata_ke;
            return [
                'lokasi' => 'Ayat ' . $nomorAyat,
                'jenis_kesalahan' => $k->kategori_kesalahan,
                'catatan' => $k->catatan
            ];
        })->toArray();

        $gradeDesc = $this->getGradeDescription($nilaiRataRata);

        $csvContent = $this->generateCsvSesi(
            $siswa,
            $surah,
            $namaKelas,
            $namaGuru,
            $ayatMulai,
            $ayatSelesai,
            $nilaiTajwid,
            $nilaiKelancaran,
            $nilaiMakhroj,
            $nilaiRataRata,
            $gradeDesc,
            $koreksi,
            $sesiSurah
        );

        $filename = 'Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '-' . str_replace(' ', '-', $surah->nama_surah) . '-' . date('d-m-Y') . '.csv';

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    private function generateCsvSesi($siswa, $surah, $namaKelas, $namaGuru, $ayatMulai, $ayatSelesai, $nilaiTajwid, $nilaiKelancaran, $nilaiMakhroj, $nilaiRataRata, $gradeDesc, $koreksi, $sesiSurah)
    {
        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, ['HAFIZUNA'], ',');
        fputcsv($output, ['SD Islam Al-Azhar 27 Cibinong Bogor'], ',');
        fputcsv($output, ['Detail Sesi Setoran Hafalan'], ',');
        fputcsv($output, [], ',');

        // INFORMASI SESI
        fputcsv($output, ['INFORMASI SESI'], ',');
        fputcsv($output, ['Siswa', $siswa->nama_siswa], ',');
        fputcsv($output, ['Kelas', $namaKelas], ',');
        fputcsv($output, ['Surah', $surah->nama_surah], ',');
        fputcsv($output, ['Ayat', $ayatMulai . ' - ' . $ayatSelesai], ',');
        fputcsv($output, ['Guru Pembimbing', $namaGuru], ',');
        fputcsv($output, ['Tanggal', date('d/m/Y')], ',');
        fputcsv($output, [], ',');

        // PENILAIAN HAFALAN
        fputcsv($output, ['PENILAIAN HAFALAN'], ',');
        fputcsv($output, ['Aspek', 'Nilai', 'Keterangan'], ',');
        fputcsv($output, ['Tajwid', $nilaiTajwid, $this->getGradeDescription($nilaiTajwid)], ',');
        fputcsv($output, ['Kelancaran', $nilaiKelancaran, $this->getGradeDescription($nilaiKelancaran)], ',');
        fputcsv($output, ['Makhroj', $nilaiMakhroj, $this->getGradeDescription($nilaiMakhroj)], ',');
        fputcsv($output, ['RATA-RATA', $nilaiRataRata, $gradeDesc], ',');
        fputcsv($output, [], ',');

        // CATATAN KOREKSI
        if (count($koreksi) > 0) {
            fputcsv($output, ['CATATAN KOREKSI'], ',');
            fputcsv($output, ['No', 'Lokasi', 'Jenis Kesalahan', 'Catatan'], ',');
            foreach ($koreksi as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item['lokasi'] ?? '',
                    $item['jenis_kesalahan'] ?? '',
                    $item['catatan'] ?? ''
                ], ',');
            }
            fputcsv($output, [], ',');
        }

        // RIWAYAT SESI UNTUK SURAH INI
        fputcsv($output, ['RIWAYAT SESI UNTUK SURAH INI'], ',');
        fputcsv($output, ['No', 'Tanggal', 'Ayat', 'Tajwid', 'Kelancaran', 'Makhroj', 'Rata-rata'], ',');
        foreach ($sesiSurah as $index => $sesi) {
            fputcsv($output, [
                $index + 1,
                $sesi['tanggal_setor'],
                $sesi['ayat_mulai'] . '-' . $sesi['ayat_selesai'],
                $sesi['skor_tajwid'],
                $sesi['skor_kelancaran'],
                $sesi['skor_makhroj'],
                $sesi['nilai_rata']
            ], ',');
        }

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
