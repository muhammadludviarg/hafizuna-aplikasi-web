<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 60px;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #16A34A;
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
            font-size: 10px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 8px 0 0 0;
            color: #333;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 2px solid #16A34A;
            padding-bottom: 3px;
        }

        .info-row {
            display: flex;
            margin: 4px 0;
            font-size: 11px;
        }

        .info-label {
            width: 140px;
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
            padding: 7px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #16A34A;
        }

        td {
            padding: 5px 7px;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .rating-label {
            font-weight: bold;
        }

        .bg-light {
            background-color: #e8f5e9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        /* Tambahkan style untuk kolom spesifik */
        .col-no {
            width: 5%;
            text-align: center;
        }

        .col-lokasi {
            width: 15%;
        }

        .col-kata {
            width: 10%;
            text-align: center;
        }

        .col-lafadz {
            width: 20%;
            text-align: right;
            font-size: 14px;
        }

        .col-jenis {
            width: 20%;
        }

        .col-catatan {
            width: 30%;
        }

        /* Font khusus Arab jika tersedia, fallback ke sans-serif */
        .arabic-text {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: right;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $sekolah }}</h1>
        <p>{{ $nama_sekolah_lengkap }}</p>
        <h2>{{ $judul }}</h2>
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
                <th style="width: 35%;">Aspek</th>
                <th style="width: 20%; text-align: center;">Nilai</th>
                <th style="width: 45%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tajwid</td>
                <td class="text-center">{{ $nilai_tajwid }}</td>
                <td>{{ $grade_tajwid ?? '' }}</td>
            </tr>
            <tr>
                <td>Kelancaran</td>
                <td class="text-center">{{ $nilai_kelancaran }}</td>
                <td>{{ $grade_kelancaran ?? '' }}</td>
            </tr>
            <tr>
                <td>Makhroj</td>
                <td class="text-center">{{ $nilai_makhroj }}</td>
                <td>{{ $grade_makhroj ?? '' }}</td>
            </tr>
            <tr class="bg-light">
                <td class="rating-label">RATA-RATA</td>
                <td class="text-center rating-label">{{ $nilai_rata_rata }}</td>
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
                    <th class="col-no">No</th>
                    <th class="col-lokasi">Lokasi</th>
                    <th style="width: 15%; text-align: center;">Sesi Ke</th>
                    <th class="col-jenis">Jenis Kesalahan</th>
                    <th class="col-catatan">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($koreksi as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['lokasi'] ?? '-' }}</td>
                        <td class="text-center">{{ $item['sesi_ke'] ?? '-' }}</td>
                        <td>{{ $item['jenis_kesalahan'] ?? '-' }}</td>
                        <td class="arabic-text">
                            {{ $item['catatan'] ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- RIWAYAT SESI UNTUK SURAH INI -->
    @if(count($riwayat_sesi) > 0)
        <div class="section-title">RIWAYAT SESI UNTUK SURAH INI</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 18%; text-align: center;">Tanggal</th>
                    <th style="width: 15%; text-align: center;">Ayat</th>
                    <th style="width: 15%; text-align: center;">Tajwid</th>
                    <th style="width: 15%; text-align: center;">Kelancaran</th>
                    <th style="width: 15%; text-align: center;">Makhroj</th>
                    <th style="width: 22%; text-align: center;">Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat_sesi as $riwayat)
                    <tr>
                        <td class="text-center">{{ $riwayat['tanggal'] }}</td>
                        <td class="text-center">{{ $riwayat['ayat'] }}</td>
                        <td class="text-center">{{ $riwayat['tajwid'] }}</td>
                        <td class="text-center">{{ $riwayat['kelancaran'] }}</td>
                        <td class="text-center">{{ $riwayat['makhroj'] }}</td>
                        <td class="text-right">{{ $riwayat['rata_rata'] }}</td>
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