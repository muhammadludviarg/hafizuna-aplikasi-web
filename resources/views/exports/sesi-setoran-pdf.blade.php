<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #16A34A;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #16A34A;
            font-weight: bold;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #333;
        }
        
        .info-row {
            display: flex;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }
        
        thead {
            background-color: #16A34A;
            color: white;
        }
        
        th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #f5f5f5;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        
        .rating-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $sekolah }}</h1>
        <p>{{ $nama_sekolah_lengkap }}</p>
        <h2 style="font-size: 13px; margin: 10px 0 5px 0;">{{ $judul }}</h2>
    </div>

    <!-- INFORMASI SESI -->
    <div class="section-title">INFORMASI SESI</div>
    <div class="info-row">
        <div class="info-label">Siswa:</div>
        <div class="info-value">{{ $nama_siswa }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Kelas:</div>
        <div class="info-value">{{ $nama_kelas }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Surah:</div>
        <div class="info-value">{{ $nama_surah }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Ayat:</div>
        <div class="info-value">{{ $ayat_mulai }} - {{ $ayat_selesai }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Guru Pembimbing:</div>
        <div class="info-value">{{ $nama_guru }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Tanggal:</div>
        <div class="info-value">{{ $tanggal_sesi }}</div>
    </div>

    <!-- PENILAIAN HAFALAN -->
    <div class="section-title">PENILAIAN HAFALAN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Aspek</th>
                <th style="width: 20%;">Nilai</th>
                <th style="width: 50%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tajwid</td>
                <td style="text-align: center;">{{ $nilai_tajwid }}</td>
                <td>{{ $this->getGradeDescription($nilai_tajwid) ?? '' }}</td>
            </tr>
            <tr>
                <td>Kelancaran</td>
                <td style="text-align: center;">{{ $nilai_kelancaran }}</td>
                <td>{{ $this->getGradeDescription($nilai_kelancaran) ?? '' }}</td>
            </tr>
            <tr>
                <td>Makhroj</td>
                <td style="text-align: center;">{{ $nilai_makhroj }}</td>
                <td>{{ $this->getGradeDescription($nilai_makhroj) ?? '' }}</td>
            </tr>
            <tr style="background-color: #e8f5e9;">
                <td class="rating-label">RATA-RATA</td>
                <td style="text-align: center; font-weight: bold;">{{ $nilai_rata_rata }}</td>
                <td class="rating-label">{{ $grade_desc ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- CATATAN KOREKSI -->
    @if(count($koreksi) > 0)
        <div class="section-title">CATATAN KOREKSI</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Lokasi</th>
                    <th style="width: 25%;">Jenis Kesalahan</th>
                    <th style="width: 45%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($koreksi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['lokasi'] ?? '-' }}</td>
                        <td>{{ $item['jenis_kesalahan'] ?? '-' }}</td>
                        <td>{{ $item['catatan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
