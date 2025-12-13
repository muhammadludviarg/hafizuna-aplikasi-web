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
        $kelas = Kelas::with(['siswa.kelompok'])->find($kelasId);

        if (!$kelas) {
            return abort(404);
        }

        // Prepare data siswa dengan Progress Target
        $siswaDetail = $this->getSiswaDetailForKelas($kelas);

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
        $kelas = Kelas::with(['siswa.kelompok'])->find($kelasId);
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
        $sheet->mergeCells('A1:F1');

        // Header Tabel (Updated: Total Ayat -> Progress Target)
        $headers = ['No', 'Nama Siswa', 'Kelompok', 'Progress Target', 'Jumlah Sesi', 'Nilai Akhir'];
        $sheet->fromArray($headers, NULL, 'A5');

        // Styling Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyle);

        // Isi Data
        $row = 6;
        foreach ($siswaDetail as $index => $siswa) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $siswa['nama_siswa']);
            $sheet->setCellValue('C' . $row, $siswa['nama_kelompok']);
            $sheet->setCellValue('D' . $row, $siswa['progress_target']); // Updated
            $sheet->setCellValue('E' . $row, $siswa['jumlah_sesi']);
            $sheet->setCellValue('F' . $row, $siswa['nilai_rata_rata']);
            $row++;
        }

        // Auto Size Columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download File
        $filename = 'Laporan-Hafalan-' . str_replace(' ', '-', $kelas->nama_kelas) . '.xlsx';
        return $this->downloadXlsx($spreadsheet, $filename);
    }

    // FUNGSI UTAMA: Menghitung Progress Target (Reused Logic dari Livewire)
    private function getSiswaDetailForKelas($kelas)
    {
        return $kelas->siswa->map(function ($siswa) {
            // 1. Cari Kelompok & Target
            $kelompok = $siswa->kelompok->first(); // Asumsi 1 siswa 1 kelompok
            $namaKelompok = $kelompok ? $kelompok->nama_kelompok : '-';
            $target = $kelompok ? TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)->first() : null;

            $surahSelesaiCount = 0;
            $totalTargetSurah = 0;

            if ($target) {
                $totalTargetSurah = abs($target->id_surah_akhir - $target->id_surah_awal) + 1;
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $idSurah) {
                    $surah = Surah::find($idSurah);
                    if (!$surah)
                        continue;

                    // Cek apakah sudah ada setoran yang tuntas (ayat_selesai >= jumlah_ayat)
                    $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->where(function ($q) use ($idSurah) {
                            $q->where('id_surah_mulai', $idSurah)
                                ->orWhere('id_surah_selesai', $idSurah);
                        })
                        ->orderByDesc('ayat_selesai')
                        ->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            $progressTarget = $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target";

            // Statistik Nilai
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

            return [
                'nama_siswa' => $siswa->nama_siswa,
                'nama_kelompok' => $namaKelompok,
                'progress_target' => $progressTarget, // Field baru
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();
    }

    public function exportPdfSiswa($siswaId)
    {
        $siswa = Siswa::with(['kelas', 'kelompok'])->find($siswaId);
        if (!$siswa)
            return abort(404);

        $dataLaporan = $this->prepareLaporanSiswaData($siswaId, $siswa);

        // REVISI: Pastikan urut berdasarkan Nomor Surah
        usort($dataLaporan['surah_dihafalkan'], function ($a, $b) {
            return $a['nomor_surah'] <=> $b['nomor_surah'];
        });

        $pdf = Pdf::loadView('exports.laporan-hafalan-siswa-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Laporan Hafalan Al-Qur\'an',
            'nama_siswa' => $siswa->nama_siswa,
            'kelas' => $siswa->kelas,
            'tanggal' => date('d/m/Y'),
            'surah_dihafalkan' => $dataLaporan['surah_dihafalkan'],
            'surah_belum_dihafalkan' => $dataLaporan['surah_belum_dihafalkan'],
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

        $dataLaporan = $this->prepareLaporanSiswaData($siswaId, $siswa);

        // REVISI: Pastikan urut berdasarkan Nomor Surah
        usort($dataLaporan['surah_dihafalkan'], function ($a, $b) {
            return $a['nomor_surah'] <=> $b['nomor_surah'];
        });

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
        $sheet->setCellValue('E6', 'Nilai Akhir:');
        $sheet->setCellValue('F6', $dataLaporan['nilai_rata_rata_total']); // REVISI LABEL
        $sheet->getStyle('E4:F6')->getFont()->setBold(true);

        // TABEL 1: SURAH SUDAH DIHAFALKAN
        $row = 9;
        $sheet->setCellValue('A' . $row, 'SURAH YANG SUDAH DIHAFALKAN');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        // REVISI LABEL HEADER
        $headers = ['No', 'Nama Surah', 'Total Sesi', 'Tajwid', 'Kelancaran', 'Makhroj', 'Nilai Akhir'];
        $sheet->fromArray($headers, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A');
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
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EA580C');
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

    public function exportPdfDetailSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['siswa.kelas', 'surahMulai', 'guru.akun', 'koreksi.ayat'])->find($sesiId);
        if (!$sesi)
            return abort(404);

        $siswa = $sesi->siswa;
        $namaGuru = ($sesi->guru && $sesi->guru->akun) ? $sesi->guru->akun->nama_lengkap : '-';

        // Hitung Sesi Ke-berapa secara manual
        $urutanSesi = SesiHafalan::where('id_siswa', $sesi->id_siswa)
            ->where(function ($q) use ($sesi) {
                $q->where('id_surah_mulai', $sesi->id_surah_mulai)
                    ->orWhere('id_surah_selesai', $sesi->id_surah_mulai);
            })
            ->where('tanggal_setor', '<=', $sesi->tanggal_setor)
            ->count();

        // Data koreksi (karena single sesi, sesi_ke nya ya urutan sesi itu)
        $arabic = new Arabic();
        $allKoreksiHistory = [];
        foreach ($sesi->koreksi as $k) {
            $catatanFixed = $k->catatan ? $arabic->utf8Glyphs($k->catatan) : '-';
            $allKoreksiHistory[] = [
                'lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')),
                'sesi_ke' => $urutanSesi, // Masukkan angka urutan sesi
                'jenis_kesalahan' => $k->kategori_kesalahan ?? '-',
                'catatan' => $catatanFixed
            ];
        }

        // Data untuk tabel nilai di PDF
        $riwayatSatuSesi = [
            [
                'tanggal' => $sesi->tanggal_setor->format('d/m/Y'),
                'ayat' => $sesi->ayat_mulai . '-' . $sesi->ayat_selesai,
                'tajwid' => number_format($sesi->skor_tajwid, 1),
                'kelancaran' => number_format($sesi->skor_kelancaran, 1),
                'makhroj' => number_format($sesi->skor_makhroj, 1),
                'rata_rata' => number_format($sesi->nilai_rata, 2)
            ]
        ];

        $pdf = Pdf::loadView('exports.sesi-setoran-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'judul' => 'Detail Setoran Hafalan',
            'nama_siswa' => $siswa->nama_siswa,
            'nama_kelas' => $siswa->kelas->nama_kelas ?? '-',
            'nama_surah' => $sesi->surahMulai->nama_surah,
            'ayat_mulai' => $sesi->ayat_mulai,
            'ayat_selesai' => $sesi->ayat_selesai,
            'nama_guru' => $namaGuru,
            'tanggal_sesi' => Carbon::parse($sesi->tanggal_setor)->translatedFormat('l, d F Y'),
            'nilai_tajwid' => number_format($sesi->skor_tajwid, 1),
            'nilai_kelancaran' => number_format($sesi->skor_kelancaran, 1),
            'nilai_makhroj' => number_format($sesi->skor_makhroj, 1),
            'nilai_rata_rata' => number_format($sesi->nilai_rata, 2),
            'grade_tajwid' => $this->getGradeDescription($sesi->skor_tajwid),
            'grade_kelancaran' => $this->getGradeDescription($sesi->skor_kelancaran),
            'grade_makhroj' => $this->getGradeDescription($sesi->skor_makhroj),
            'grade_desc' => $this->getGradeDescription($sesi->nilai_rata),
            'koreksi' => $allKoreksiHistory,
            'riwayat_sesi' => $riwayatSatuSesi,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Detail-Sesi-' . $sesi->tanggal_setor->format('dmY') . '-' . str_replace(' ', '-', $siswa->nama_siswa) . '.pdf');
    }

    // EXPORT EXCEL SATU SESI (POPUP)
    public function exportExcelDetailSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['siswa.kelas', 'surahMulai', 'guru.akun', 'koreksi.ayat'])->find($sesiId);
        if (!$sesi)
            return abort(404);

        $siswa = $sesi->siswa;
        $namaGuru = ($sesi->guru && $sesi->guru->akun) ? $sesi->guru->akun->nama_lengkap : '-';
        $gradeDesc = $this->getGradeDescription($sesi->nilai_rata);

        // Hitung Sesi Ke-berapa secara manual
        $urutanSesi = SesiHafalan::where('id_siswa', $sesi->id_siswa)
            ->where(function ($q) use ($sesi) {
                $q->where('id_surah_mulai', $sesi->id_surah_mulai)
                    ->orWhere('id_surah_selesai', $sesi->id_surah_mulai);
            })
            ->where('tanggal_setor', '<=', $sesi->tanggal_setor)
            ->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'HAFIZUNA - SD Islam Al-Azhar 27 Cibinong Bogor');
        $sheet->setCellValue('A2', 'Detail Setoran Hafalan');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Info
        $sheet->setCellValue('A4', 'Nama Siswa:');
        $sheet->setCellValue('B4', $siswa->nama_siswa);
        $sheet->setCellValue('A5', 'Kelas:');
        $sheet->setCellValue('B5', $siswa->kelas->nama_kelas ?? '-');
        $sheet->setCellValue('A6', 'Surah:');
        $sheet->setCellValue('B6', $sesi->surahMulai->nama_surah);
        $sheet->setCellValue('A7', 'Guru:');
        $sheet->setCellValue('B7', $namaGuru);
        $sheet->setCellValue('A8', 'Tanggal:');
        $sheet->setCellValue('B8', $sesi->tanggal_setor->format('d/m/Y'));

        // Nilai
        $sheet->setCellValue('D4', 'HASIL PENILAIAN');
        $sheet->getStyle('D4')->getFont()->setBold(true);
        $sheet->setCellValue('D5', 'Tajwid:');
        $sheet->setCellValue('E5', $sesi->skor_tajwid);
        $sheet->setCellValue('D6', 'Kelancaran:');
        $sheet->setCellValue('E6', $sesi->skor_kelancaran);
        $sheet->setCellValue('D7', 'Makhroj:');
        $sheet->setCellValue('E7', $sesi->skor_makhroj);
        $sheet->setCellValue('D8', 'Nilai Akhir:');
        $sheet->setCellValue('E8', $sesi->nilai_rata . ' (' . $gradeDesc . ')');

        // Koreksi
        $row = 11;
        $sheet->setCellValue('A' . $row, 'CATATAN KOREKSI');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $headers = ['No', 'Tanggal', 'Sesi Ke-', 'Lokasi Ayat', 'Jenis Kesalahan', 'Catatan (Lafadz)'];
        $sheet->fromArray($headers, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A');

        $row++;
        $no = 1;
        if ($sesi->koreksi->count() > 0) {
            foreach ($sesi->koreksi as $k) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $sesi->tanggal_setor->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $urutanSesi); // ISI KOLOM SESI KE-
                $sheet->setCellValue('D' . $row, 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')));
                $sheet->setCellValue('E' . $row, $k->kategori_kesalahan ?? '-');
                $sheet->setCellValue('F' . $row, $k->catatan ?? '-');
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Tidak ada catatan koreksi.');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return $this->downloadXlsx($spreadsheet, 'Detail-Sesi-' . $sesi->tanggal_setor->format('dmY') . '.xlsx');
    }

    public function exportPdfSesi($siswaId, $surahId)
    { /* SAMA SEPERTI SEBELUMNYA, TIDAK DIUBAH AGAR TAB DETAIL SURAH JALAN */
        $siswa = Siswa::with(['kelas', 'kelompok.guru.akun'])->find($siswaId);
        $surah = Surah::find($surahId);
        if (!$siswa || !$surah)
            return abort(404);
        $namaGuru = '-';
        $kelompokSiswa = $siswa->kelompok->first();
        if ($kelompokSiswa && $kelompokSiswa->guru && $kelompokSiswa->guru->akun) {
            $namaGuru = $kelompokSiswa->guru->akun->nama_lengkap;
        }
        $allSesi = SesiHafalan::where('id_siswa', $siswaId)->where(function ($q) use ($surahId) {
            $q->where('id_surah_mulai', $surahId)->orWhere('id_surah_selesai', $surahId);
        })->with(['koreksi.ayat'])->orderBy('tanggal_setor', 'asc')->get();
        if ($allSesi->isEmpty())
            return abort(404, 'Sesi tidak ditemukan');
        $latestSesi = $allSesi->last();
        $arabic = new Arabic();
        $allKoreksiHistory = [];
        foreach ($allSesi as $index => $sesi) {
            $nomorSesi = $index + 1;
            foreach ($sesi->koreksi as $k) {
                $catatanFixed = $k->catatan ? $arabic->utf8Glyphs($k->catatan) : '-';
                $allKoreksiHistory[] = ['lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')), 'sesi_ke' => $nomorSesi, 'jenis_kesalahan' => $k->kategori_kesalahan ?? '-', 'catatan' => $catatanFixed];
            }
        }
        $allKoreksiHistory = array_reverse($allKoreksiHistory);
        $riwayatSesiFormatted = $allSesi->sortByDesc('tanggal_setor')->map(function ($sesi) {
            return ['tanggal' => $sesi->tanggal_setor->format('d/m/Y'), 'ayat' => $sesi->ayat_mulai . '-' . $sesi->ayat_selesai, 'tajwid' => number_format($sesi->skor_tajwid, 1), 'kelancaran' => number_format($sesi->skor_kelancaran, 1), 'makhroj' => number_format($sesi->skor_makhroj, 1), 'rata_rata' => number_format($sesi->nilai_rata, 2)];
        })->values()->toArray();
        $pdf = Pdf::loadView('exports.sesi-setoran-pdf', [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27 Cibinong Bogor',
            'judul' => 'Riwayat Koreksi Hafalan',
            'nama_siswa' => $siswa->nama_siswa,
            'nama_kelas' => $siswa->kelas->nama_kelas ?? '-',
            'nama_surah' => $surah->nama_surah,
            'ayat_mulai' => $latestSesi->ayat_mulai,
            'ayat_selesai' => $latestSesi->ayat_selesai,
            'nama_guru' => $namaGuru,
            'tanggal_sesi' => Carbon::parse($latestSesi->tanggal_setor)->translatedFormat('l, d F Y'),
            'nilai_tajwid' => number_format($latestSesi->skor_tajwid, 1),
            'nilai_kelancaran' => number_format($latestSesi->skor_kelancaran, 1),
            'nilai_makhroj' => number_format($latestSesi->skor_makhroj, 1),
            'nilai_rata_rata' => number_format($latestSesi->nilai_rata, 2),
            'grade_tajwid' => $this->getGradeDescription($latestSesi->skor_tajwid),
            'grade_kelancaran' => $this->getGradeDescription($latestSesi->skor_kelancaran),
            'grade_makhroj' => $this->getGradeDescription($latestSesi->skor_makhroj),
            'grade_desc' => $this->getGradeDescription($latestSesi->nilai_rata),
            'koreksi' => $allKoreksiHistory,
            'riwayat_sesi' => $riwayatSesiFormatted,
        ])->setPaper('a4', 'portrait');
        return $pdf->download('Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '.pdf');
    }

    public function exportExcelSesi($siswaId, $surahId)
    { /* SAMA SEPERTI SEBELUMNYA */
        $siswa = Siswa::with(['kelas', 'kelompok.guru.akun'])->find($siswaId);
        $surah = Surah::find($surahId);
        if (!$siswa || !$surah)
            return abort(404);
        $allSesi = SesiHafalan::where('id_siswa', $siswaId)->where(function ($q) use ($surahId) {
            $q->where('id_surah_mulai', $surahId)->orWhere('id_surah_selesai', $surahId);
        })->with(['koreksi.ayat'])->orderBy('tanggal_setor', 'asc')->get();
        if ($allSesi->isEmpty())
            return abort(404, 'Sesi tidak ditemukan');
        $namaGuru = '-';
        $kelompokSiswa = $siswa->kelompok->first();
        if ($kelompokSiswa && $kelompokSiswa->guru && $kelompokSiswa->guru->akun) {
            $namaGuru = $kelompokSiswa->guru->akun->nama_lengkap;
        }
        $latestSesi = $allSesi->last();
        $gradeDesc = $this->getGradeDescription($latestSesi->nilai_rata);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'HAFIZUNA - SD Islam Al-Azhar 27 Cibinong Bogor');
        $sheet->setCellValue('A2', 'Detail Riwayat Sesi Setoran Hafalan');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
        $sheet->setCellValue('D4', 'NILAI TERAKHIR');
        $sheet->getStyle('D4')->getFont()->setBold(true);
        $sheet->setCellValue('D5', 'Tajwid:');
        $sheet->setCellValue('E5', $latestSesi->skor_tajwid);
        $sheet->setCellValue('D6', 'Kelancaran:');
        $sheet->setCellValue('E6', $latestSesi->skor_kelancaran);
        $sheet->setCellValue('D7', 'Makhroj:');
        $sheet->setCellValue('E7', $latestSesi->skor_makhroj);
        $sheet->setCellValue('D8', 'Rata-rata:');
        $sheet->setCellValue('E8', $latestSesi->nilai_rata . ' (' . $gradeDesc . ')');
        $row = 11;
        $sheet->setCellValue('A' . $row, 'RIWAYAT CATATAN KOREKSI');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $headers = ['No', 'Tanggal', 'Sesi Ke-', 'Lokasi Ayat', 'Jenis Kesalahan', 'Catatan (Lafadz)'];
        $sheet->fromArray($headers, NULL, 'A' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('16A34A');
        $row++;
        $koreksiFound = false;
        $sesiReverse = $allSesi->reverse();
        $no = 1;
        foreach ($sesiReverse as $index => $sesi) {
            $sesiKe = $allSesi->search(function ($item) use ($sesi) {
                return $item->id_sesi === $sesi->id_sesi;
            }) + 1;
            foreach ($sesi->koreksi as $k) {
                $koreksiFound = true;
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $sesi->tanggal_setor->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $sesiKe);
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
        $row += 2;
        $sheet->setCellValue('A' . $row, 'REKAPITULASI NILAI PER PERTEMUAN');
        $sheet->mergeCells('A' . $row . ':G' . $row);
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
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return $this->downloadXlsx($spreadsheet, 'Detail-Sesi-' . str_replace(' ', '-', $siswa->nama_siswa) . '.xlsx');
    }

    private function prepareLaporanSiswaData($siswaId, $siswa)
    {
        $semuaSesi = SesiHafalan::where('id_siswa', $siswaId)
            ->with(['surahMulai'])
            ->orderBy('tanggal_setor', 'asc')
            ->get();

        $statsSurah = [];
        $totalSesi = $semuaSesi->count();
        $totalAyat = 0;
        $nilaiTotal = 0;

        foreach ($semuaSesi as $sesi) {
            $idSurah = $sesi->id_surah_mulai;
            $surah = $sesi->surahMulai;
            $totalAyat += ($sesi->ayat_selesai - $sesi->ayat_mulai + 1);
            $nilaiTotal += $sesi->nilai_rata;

            // FIX: Tambahkan 'nomor_surah' saat inisialisasi
            if (!isset($statsSurah[$idSurah])) {
                $statsSurah[$idSurah] = [
                    'nama_surah' => $surah ? $surah->nama_surah : 'Unknown',
                    'nomor_surah' => $surah ? $surah->nomor_surah : 999, // INI YANG BIKIN ERROR SEBELUMNYA
                    'jumlah_ayat_surah' => $surah ? $surah->jumlah_ayat : 0,
                    'count' => 0,
                    'max_ayat' => 0,
                    'latest_scores' => []
                ];
            }

            $statsSurah[$idSurah]['count']++;
            if ($sesi->ayat_selesai > $statsSurah[$idSurah]['max_ayat']) {
                $statsSurah[$idSurah]['max_ayat'] = $sesi->ayat_selesai;
            }
            $statsSurah[$idSurah]['latest_scores'] = [
                'tajwid' => $sesi->skor_tajwid,
                'kelancaran' => $sesi->skor_kelancaran,
                'makhroj' => $sesi->skor_makhroj,
                'rata_rata' => $sesi->nilai_rata
            ];
        }

        $surahDihafalkan = [];
        $progressMap = [];

        foreach ($statsSurah as $idSurah => $stat) {
            if ($stat['max_ayat'] >= $stat['jumlah_ayat_surah']) {
                $surahDihafalkan[] = [
                    'nomor_surah' => $stat['nomor_surah'], // Sekarang aman
                    'nama_surah' => $stat['nama_surah'],
                    'jumlah_sesi' => $stat['count'],
                    'nilai_tajwid' => $stat['latest_scores']['tajwid'],
                    'nilai_kelancaran' => $stat['latest_scores']['kelancaran'],
                    'nilai_makhroj' => $stat['latest_scores']['makhroj'],
                    'nilai_rata' => $stat['latest_scores']['rata_rata'],
                ];
            } else {
                $progressMap[$idSurah] = $stat['max_ayat'];
            }
        }

        $surahBelumDihafalkan = [];
        $kelompokIds = $siswa->kelompok->pluck('id_kelompok');
        $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();
        $allTargetSurahIds = [];
        foreach ($targetHafalan as $target) {
            $range = range($target->id_surah_awal, $target->id_surah_akhir);
            $allTargetSurahIds = array_merge($allTargetSurahIds, $range);
        }
        $allTargetSurahIds = array_unique($allTargetSurahIds);

        foreach ($allTargetSurahIds as $idSurah) {
            if (isset($statsSurah[$idSurah]) && $statsSurah[$idSurah]['max_ayat'] >= $statsSurah[$idSurah]['jumlah_ayat_surah']) {
                continue;
            }
            $surah = Surah::find($idSurah);
            if ($surah) {
                if (isset($progressMap[$idSurah])) {
                    $status = 'Belum Selesai';
                    $progress = $progressMap[$idSurah] . '/' . $surah->jumlah_ayat . ' ayat';
                } else {
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

    public function exportPdfKelompok($kelompokId)
    {
        $kelompok = Kelompok::with(['kelas', 'siswa'])->find($kelompokId);

        if (!$kelompok) {
            return abort(404);
        }

        // Ambil HANYA siswa yang tergabung dalam kelompok ini
        $siswaList = $kelompok->siswa;

        // Prepare data siswa dengan Progress Target
        $siswaDetail = $siswaList->map(function ($siswa) use ($kelompok) {
            // 1. Ambil target dari kelompok ini
            $target = TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)->first();

            $surahSelesaiCount = 0;
            $totalTargetSurah = 0;

            if ($target) {
                $totalTargetSurah = abs($target->id_surah_akhir - $target->id_surah_awal) + 1;
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $idSurah) {
                    $surah = Surah::find($idSurah);
                    if (!$surah)
                        continue;

                    $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->where(function ($q) use ($idSurah) {
                            $q->where('id_surah_mulai', $idSurah)
                                ->orWhere('id_surah_selesai', $idSurah);
                        })
                        ->orderByDesc('ayat_selesai')
                        ->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            $progressTarget = $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target";

            // Statistik Nilai
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

            return [
                'nama_siswa' => $siswa->nama_siswa,
                'progress_target' => $progressTarget,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();

        $data = [
            'sekolah' => 'HAFIZUNA',
            'nama_sekolah_lengkap' => 'SD Islam Al-Azhar 27',
            'lokasi' => 'Cibinong Bogor',
            'judul' => 'Laporan Hafalan Per Kelompok',
            'nama_kelas' => "Kelompok " . ($kelompok->nama_kelompok ?? '-') . " - " . ($kelompok->kelas->nama_kelas ?? '-'),
            'tahun_ajaran' => $kelompok->kelas->tahun_ajaran ?? '',
            'tanggal' => date('d/m/Y'),
            'jumlah_siswa' => count($siswaDetail),
            'siswa_data' => $siswaDetail,
        ];

        $pdf = Pdf::loadView('exports.laporan-hafalan-pdf', $data);

        return $pdf->download('Laporan-Hafalan-Kelompok-' . str_replace(' ', '-', $kelompok->nama_kelompok) . '-' . date('dmY') . '.pdf');
    }

    public function exportExcelKelompok($kelompokId)
    {
        $kelompok = Kelompok::with(['kelas', 'siswa'])->find($kelompokId);
        if (!$kelompok)
            return abort(404);

        // Ambil HANYA siswa yang tergabung dalam kelompok ini
        $siswaList = $kelompok->siswa;

        // Prepare data siswa dengan Progress Target
        $siswaDetail = $siswaList->map(function ($siswa) use ($kelompok) {
            $target = TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)->first();

            $surahSelesaiCount = 0;
            $totalTargetSurah = 0;

            if ($target) {
                $totalTargetSurah = abs($target->id_surah_akhir - $target->id_surah_awal) + 1;
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $idSurah) {
                    $surah = Surah::find($idSurah);
                    if (!$surah)
                        continue;

                    $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->where(function ($q) use ($idSurah) {
                            $q->where('id_surah_mulai', $idSurah)
                                ->orWhere('id_surah_selesai', $idSurah);
                        })
                        ->orderByDesc('ayat_selesai')
                        ->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            $progressTarget = $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target";

            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

            return [
                'nama_siswa' => $siswa->nama_siswa,
                'progress_target' => $progressTarget,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();

        // Buat Spreadsheet Baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Judul
        $sheet->setCellValue('A1', 'Laporan Hafalan Per Kelompok');
        $sheet->setCellValue('A2', 'Kelompok: ' . ($kelompok->nama_kelompok ?? '-') . ' - ' . ($kelompok->kelas->nama_kelas ?? '-'));
        $sheet->setCellValue('A3', 'Tanggal: ' . date('d/m/Y'));
        $sheet->mergeCells('A1:E1');

        // Header Tabel
        $headers = ['No', 'Nama Siswa', 'Progress Target', 'Jumlah Sesi', 'Nilai Akhir'];
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
            $sheet->setCellValue('C' . $row, $siswa['progress_target']);
            $sheet->setCellValue('D' . $row, $siswa['jumlah_sesi']);
            $sheet->setCellValue('E' . $row, $siswa['nilai_rata_rata']);
            $row++;
        }

        // Auto Size Columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download File
        $filename = 'Laporan-Hafalan-Kelompok-' . str_replace(' ', '-', $kelompok->nama_kelompok) . '.xlsx';
        return $this->downloadXlsx($spreadsheet, $filename);
    }
}