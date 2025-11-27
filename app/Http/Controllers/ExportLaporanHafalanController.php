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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        if (!$kelas)
            return abort(404);

        $siswaDetail = $this->getSiswaDetailForKelas($kelas);

        // Buat Spreadsheet Baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Judul
        $sheet->setCellValue('A1', 'Laporan Hafalan Per Kelas');
        $sheet->setCellValue('A2', 'Kelas: ' . $kelas->nama_kelas);
        $sheet->setCellValue('A3', 'Tanggal: ' . date('d/m/Y'));
        $sheet->mergeCells('A1:E1');

        // Header Tabel
        $headers = ['No', 'Nama Siswa', 'Total Ayat', 'Jumlah Sesi', 'Nilai Rata-rata'];
        $sheet->fromArray($headers, NULL, 'A5');

        // Styling Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A5:E5')->applyFromArray($headerStyle);

        // Isi Data
        $row = 6;
        foreach ($siswaDetail as $index => $siswa) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $siswa['nama_siswa']);
            $sheet->setCellValue('C' . $row, $siswa['total_ayat']);
            $sheet->setCellValue('D' . $row, $siswa['jumlah_sesi']);
            $sheet->setCellValue('E' . $row, $siswa['nilai_rata_rata']);
            $row++;
        }

        // Auto Size Columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download File
        $filename = 'Laporan-Hafalan-' . str_replace(' ', '-', $kelas->nama_kelas) . '.xlsx';
        return $this->downloadXlsx($spreadsheet, $filename);
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
        $siswa = Siswa::with(['kelas', 'kelompok'])->find($siswaId);
        if (!$siswa)
            return abort(404);

        // 1. Proses Grouping Data
        $dataLaporan = $this->prepareLaporanSiswaData($siswaId, $siswa);

        // 2. Generate PDF
        $pdf = Pdf::loadView('exports.laporan-hafalan-siswa-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Laporan Hafalan Al-Qur\'an',
            'nama_siswa' => $siswa->nama_siswa,
            'kelas' => $siswa->kelas,
            'tanggal' => date('d/m/Y'),

            // Data Tabel
            'surah_dihafalkan' => $dataLaporan['surah_dihafalkan'],
            'surah_belum_dihafalkan' => $dataLaporan['surah_belum_dihafalkan'],

            // Statistik Umum
            'jumlah_sesi' => $dataLaporan['total_sesi'],
            'total_ayat' => $dataLaporan['total_ayat'],
            'nilai_rata_rata' => $dataLaporan['nilai_rata_rata_total'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Hafalan-' . str_replace(' ', '-', $siswa->nama_siswa) . '.pdf');
    }

    public function exportExcelSiswa($siswaId)
    {
        $siswa = Siswa::with(['kelas', 'kelompok'])->find($siswaId);
        if (!$siswa)
            return abort(404);

        // 1. Proses Grouping Data
        $dataLaporan = $this->prepareLaporanSiswaData($siswaId, $siswa);

        // 2. Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'HAFIZUNA - SD Islam Al-Azhar 27 Cibinong Bogor');
        $sheet->setCellValue('A2', 'Laporan Hafalan Al-Qur\'an');
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Info Siswa
        $sheet->setCellValue('A4', 'Nama Siswa:');
        $sheet->setCellValue('B4', $siswa->nama_siswa);
        $sheet->setCellValue('A5', 'Kelas:');
        $sheet->setCellValue('B5', $siswa->kelas->nama_kelas ?? '-');
        $sheet->setCellValue('A6', 'Tanggal:');
        $sheet->setCellValue('B6', date('d/m/Y'));

        // Statistik
        $sheet->setCellValue('E4', 'Total Sesi:');
        $sheet->setCellValue('F4', $dataLaporan['total_sesi']);
        $sheet->setCellValue('E5', 'Total Ayat:');
        $sheet->setCellValue('F5', $dataLaporan['total_ayat']);
        $sheet->setCellValue('E6', 'Rata-rata:');
        $sheet->setCellValue('F6', $dataLaporan['nilai_rata_rata_total']);
        $sheet->getStyle('E4:F6')->getFont()->setBold(true);

        // TABEL 1: SURAH SUDAH DIHAFALKAN
        $row = 9;
        $sheet->setCellValue('A' . $row, 'SURAH YANG SUDAH DIHAFALKAN');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $headers = ['No', 'Nama Surah', 'Total Sesi', 'Tajwid', 'Kelancaran', 'Makhroj', 'Nilai Terakhir'];
        $sheet->fromArray($headers, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A'); // Hijau
        $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $no = 1;
        if (count($dataLaporan['surah_dihafalkan']) > 0) {
            foreach ($dataLaporan['surah_dihafalkan'] as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nama_surah']);
                $sheet->setCellValue('C' . $row, $item['jumlah_sesi']);
                $sheet->setCellValue('D' . $row, $item['nilai_tajwid']);
                $sheet->setCellValue('E' . $row, $item['nilai_kelancaran']);
                $sheet->setCellValue('F' . $row, $item['nilai_makhroj']);
                $sheet->setCellValue('G' . $row, $item['nilai_rata']);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Belum ada hafalan yang selesai.');
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $row += 2;

        // TABEL 2: TARGET BELUM DIHAFALKAN
        $sheet->setCellValue('A' . $row, 'TARGET HAFALAN YANG BELUM DIHAFALKAN');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $headersTarget = ['No', 'Nama Surah', 'Status', 'Progress'];
        $sheet->fromArray($headersTarget, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EA580C'); // Oranye
        $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $no = 1;
        if (count($dataLaporan['surah_belum_dihafalkan']) > 0) {
            foreach ($dataLaporan['surah_belum_dihafalkan'] as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nama_surah']);
                $sheet->setCellValue('C' . $row, $item['status']);
                $sheet->setCellValue('D' . $row, $item['progress']);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Semua target hafalan sudah selesai.');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->downloadXlsx($spreadsheet, 'Laporan-Siswa-' . str_replace(' ', '-', $siswa->nama_siswa) . '.xlsx');
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
    public function exportExcelSesi($siswaId, $surahId)
    {
        $siswa = Siswa::with(['kelas', 'kelompok.guru'])->find($siswaId);
        $surah = Surah::find($surahId);

        if (!$siswa || !$surah)
            return abort(404);

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

        // Ambil Guru
        $namaGuru = '-';
        $kelompokSiswa = $siswa->kelompok->first();
        if ($kelompokSiswa && $kelompokSiswa->guru) {
            $namaGuru = $kelompokSiswa->guru->nama_guru;
        }

        $latestSesi = $allSesi->last();
        $nilaiRataRata = $latestSesi->nilai_rata ?? 0;
        $gradeDesc = $this->getGradeDescription($nilaiRataRata);

        // === MEMBUAT SPREADSHEET EXCEL ===
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Header Laporan
        $sheet->setCellValue('A1', 'HAFIZUNA - SD Islam Al-Azhar 27 Cibinong Bogor');
        $sheet->setCellValue('A2', 'Detail Riwayat Sesi Setoran Hafalan');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Informasi Siswa
        $sheet->setCellValue('A4', 'Nama Siswa:');
        $sheet->setCellValue('B4', $siswa->nama_siswa);
        $sheet->setCellValue('A5', 'Kelas:');
        $sheet->setCellValue('B5', $siswa->kelas->nama_kelas ?? '-');
        $sheet->setCellValue('A6', 'Surah:');
        $sheet->setCellValue('B6', $surah->nama_surah);
        $sheet->setCellValue('A7', 'Guru:');
        $sheet->setCellValue('B7', $namaGuru);
        $sheet->setCellValue('A8', 'Tanggal:');
        $sheet->setCellValue('B8', $latestSesi->tanggal_setor->format('d/m/Y'));

        // 3. Nilai Terakhir
        $sheet->setCellValue('D4', 'NILAI TERAKHIR');
        $sheet->getStyle('D4')->getFont()->setBold(true);
        $sheet->setCellValue('D5', 'Tajwid:');
        $sheet->setCellValue('E5', $latestSesi->skor_tajwid);
        $sheet->setCellValue('D6', 'Kelancaran:');
        $sheet->setCellValue('E6', $latestSesi->skor_kelancaran);
        $sheet->setCellValue('D7', 'Makhroj:');
        $sheet->setCellValue('E7', $latestSesi->skor_makhroj);
        $sheet->setCellValue('D8', 'Rata-rata:');
        $sheet->setCellValue('E8', $nilaiRataRata . ' (' . $gradeDesc . ')');

        // 4. Tabel Riwayat Koreksi
        $row = 11;
        $sheet->setCellValue('A' . $row, 'RIWAYAT CATATAN KOREKSI');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $headers = ['No', 'Tanggal', 'Sesi Ke-', 'Lokasi Ayat', 'Jenis Kesalahan', 'Catatan (Lafadz)'];
        $sheet->fromArray($headers, NULL, 'A' . $row);

        // Style Header Tabel
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A');

        // Isi Data Koreksi
        $row++;
        $koreksiFound = false;
        // Urutkan data sesi untuk koreksi (Terbaru -> Terlama)
        $sesiReverse = $allSesi->reverse();

        $no = 1;
        foreach ($sesiReverse as $index => $sesi) {
            // index di reverse collection tetap mempertahankan key aslinya (misal id), jadi kita hitung manual untuk sesi ke-
            // Logic Sesi Ke: Kita perlu cari index asli dari $allSesi
            $sesiKe = $allSesi->search(function ($item) use ($sesi) {
                return $item->id_sesi === $sesi->id_sesi;
            }) + 1;

            foreach ($sesi->koreksi as $k) {
                $koreksiFound = true;
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $sesi->tanggal_setor->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $sesiKe); // Sesi Ke
                $sheet->setCellValue('D' . $row, 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')));
                $sheet->setCellValue('E' . $row, $k->kategori_kesalahan ?? '-');
                $sheet->setCellValue('F' . $row, $k->catatan ?? '-');
                $row++;
            }
        }

        if (!$koreksiFound) {
            $sheet->setCellValue('A' . $row, 'Tidak ada catatan koreksi.');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
        }

        $row += 2; // Spasi

        // 5. Tabel Riwayat Nilai
        $sheet->setCellValue('A' . $row, 'REKAPITULASI NILAI PER PERTEMUAN');
        $sheet->mergeCells('A' . $row . ':G' . $row); // G untuk rata-rata
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $headersNilai = ['No', 'Tanggal', 'Ayat', 'Tajwid', 'Kelancaran', 'Makhroj', 'Rata-rata'];
        $sheet->fromArray($headersNilai, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A');

        $row++;
        $no = 1;
        foreach ($sesiReverse as $sesi) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $sesi->tanggal_setor->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $sesi->ayat_mulai . '-' . $sesi->ayat_selesai);
            $sheet->setCellValue('D' . $row, $sesi->skor_tajwid);
            $sheet->setCellValue('E' . $row, $sesi->skor_kelancaran);
            $sheet->setCellValue('F' . $row, $sesi->skor_makhroj);
            $sheet->setCellValue('G' . $row, $sesi->nilai_rata);
            $row++;
        }

        // Auto Size Columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download Xlsx
        $filename = 'Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '.xlsx';
        return $this->downloadXlsx($spreadsheet, $filename);
    }

    private function prepareLaporanSiswaData($siswaId, $siswa)
    {
        // 1. Ambil Semua Sesi (Urut terlama -> terbaru untuk dapet nilai terakhir)
        $semuaSesi = SesiHafalan::where('id_siswa', $siswaId)
            ->with(['surahMulai'])
            ->orderBy('tanggal_setor', 'asc')
            ->get();

        $statsSurah = [];
        $totalSesi = $semuaSesi->count();
        $totalAyat = 0;
        $nilaiTotal = 0;

        // 2. Grouping per Surah
        foreach ($semuaSesi as $sesi) {
            $idSurah = $sesi->id_surah_mulai; // Asumsi input per surah
            $surah = $sesi->surahMulai;

            // Hitung total ayat global
            $totalAyat += ($sesi->ayat_selesai - $sesi->ayat_mulai + 1);
            $nilaiTotal += $sesi->nilai_rata;

            // Inisialisasi Data Surah jika belum ada
            if (!isset($statsSurah[$idSurah])) {
                $statsSurah[$idSurah] = [
                    'nama_surah' => $surah ? $surah->nama_surah : 'Unknown',
                    'jumlah_ayat_surah' => $surah ? $surah->jumlah_ayat : 0,
                    'count' => 0, // Jumlah sesi untuk surah ini
                    'max_ayat' => 0, // Progress terjauh
                    'latest_scores' => [] // Nilai dari sesi terakhir
                ];
            }

            // Update Data Surah
            $statsSurah[$idSurah]['count']++; // Tambah sesi

            // Cek progress
            if ($sesi->ayat_selesai > $statsSurah[$idSurah]['max_ayat']) {
                $statsSurah[$idSurah]['max_ayat'] = $sesi->ayat_selesai;
            }

            // Selalu update nilai dengan sesi saat ini (karena loop ASC, ini akan jadi nilai terakhir)
            $statsSurah[$idSurah]['latest_scores'] = [
                'tajwid' => $sesi->skor_tajwid,
                'kelancaran' => $sesi->skor_kelancaran,
                'makhroj' => $sesi->skor_makhroj,
                'rata_rata' => $sesi->nilai_rata
            ];
        }

        // 3. Pisahkan Jadi 2 Kategori
        $surahDihafalkan = [];
        $progressMap = []; // Untuk lookup progress surah yg belum selesai

        foreach ($statsSurah as $idSurah => $stat) {
            // TUNTAS jika ayat terakhir yg disetor >= jumlah ayat surah
            if ($stat['max_ayat'] >= $stat['jumlah_ayat_surah']) {
                $surahDihafalkan[] = [
                    'nama_surah' => $stat['nama_surah'],
                    'jumlah_sesi' => $stat['count'], // Jumlah Sesi (misal 5 kali)
                    'nilai_tajwid' => $stat['latest_scores']['tajwid'],
                    'nilai_kelancaran' => $stat['latest_scores']['kelancaran'],
                    'nilai_makhroj' => $stat['latest_scores']['makhroj'],
                    'nilai_rata' => $stat['latest_scores']['rata_rata'],
                ];
            } else {
                // Masuk kategori Belum Selesai
                $progressMap[$idSurah] = $stat['max_ayat'];
            }
        }

        // 4. Proses Target Hafalan (Gabung Belum Dimulai + Belum Selesai)
        $surahBelumDihafalkan = [];
        $kelompokIds = $siswa->kelompok->pluck('id_kelompok');
        $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

        // Kumpulkan semua ID surah target unik
        $allTargetSurahIds = [];
        foreach ($targetHafalan as $target) {
            $range = range($target->id_surah_awal, $target->id_surah_akhir);
            $allTargetSurahIds = array_merge($allTargetSurahIds, $range);
        }
        $allTargetSurahIds = array_unique($allTargetSurahIds);

        foreach ($allTargetSurahIds as $idSurah) {
            // Skip jika sudah TUNTAS
            if (isset($statsSurah[$idSurah]) && $statsSurah[$idSurah]['max_ayat'] >= $statsSurah[$idSurah]['jumlah_ayat_surah']) {
                continue;
            }

            $surah = Surah::find($idSurah);
            if ($surah) {
                if (isset($progressMap[$idSurah])) {
                    // Ada di progress map = Sedang Berjalan
                    $status = 'Belum Selesai';
                    $progress = $progressMap[$idSurah] . '/' . $surah->jumlah_ayat . ' ayat';
                } else {
                    // Tidak ada di progress map = Belum Dimulai
                    $status = 'Belum Dimulai';
                    $progress = '0/' . $surah->jumlah_ayat . ' ayat';
                }

                $surahBelumDihafalkan[] = [
                    'nama_surah' => $surah->nama_surah,
                    'status' => $status,
                    'progress' => $progress,
                ];
            }
        }

        return [
            'surah_dihafalkan' => $surahDihafalkan,
            'surah_belum_dihafalkan' => $surahBelumDihafalkan,
            'total_sesi' => $totalSesi,
            'total_ayat' => $totalAyat,
            'nilai_rata_rata_total' => $totalSesi > 0 ? round($nilaiTotal / $totalSesi, 2) : 0
        ];
    }

    private function downloadXlsx($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function getSiswaDetailForKelas($kelas)
    {
        return $kelas->siswa->map(function ($siswa) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;
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
        })->sortByDesc('nilai_rata_rata')->values()->toArray();
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
