<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanHafalanExport
{
    protected $data;
    protected $kelasName;
    protected $tahunAjaran;

    public function __construct($data, $kelasName, $tahunAjaran)
    {
        $this->data = $data;
        $this->kelasName = $kelasName;
        $this->tahunAjaran = $tahunAjaran;
    }

    public function toSpreadsheet()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Laporan Hafalan');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '16A34A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue('A2', 'Kelas: ' . $this->kelasName);
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . $this->tahunAjaran);
        $sheet->setCellValue('A4', 'Jumlah Siswa: ' . count($this->data));

        // Header row
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Nama Siswa');
        $sheet->setCellValue('C6', 'Surah Selesai');
        $sheet->setCellValue('D6', 'Total Sesi');
        $sheet->setCellValue('E6', 'Rata-rata Nilai');

        // Apply header styling
        $sheet->getStyle('A6:E6')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16A34A'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add data rows
        $row = 7;
        $no = 1;
        foreach ($this->data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nama_siswa']);
            $sheet->setCellValue('C' . $row, $item['total_ayat'] ?? 0);
            $sheet->setCellValue('D' . $row, $item['jumlah_sesi'] ?? 0);
            $sheet->setCellValue('E' . $row, $item['nilai_rata_rata'] ?? 0);

            // Center align number columns
            $sheet->getStyle('A' . $row . ':A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);

        return $spreadsheet;
    }

    public function toCsv()
    {
        return $this->data;
    }

    public function getHeaders()
    {
        return [
            'No',
            'Nama Siswa',
            'Surah Selesai',
            'Total Sesi',
            'Nilai Rata-Rata'
        ];
    }
}
