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
            margin-bottom: 10px;
            border-bottom: 2px solid #16A34A;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #16A34A;
            font-weight: bold;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 12px;
        }
        
        .info-section {
            margin: 15px 0;
            font-size: 12px;
        }
        
        .info-row {
            display: flex;
            margin: 3px 0;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #16A34A;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        
        thead {
            background-color: #16A34A;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #f5f5f5;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $sekolah }}</h1>
        <p>{{ $nama_sekolah_lengkap }} - {{ $lokasi }}</p>
    </div>

    <div class="info-section">
        <h3 style="margin: 0 0 10px 0; font-size: 13px;">{{ $judul }}</h3>
        <div class="info-row">
            <div class="info-label">Nama Siswa:</div>
            <div class="info-value">{{ $nama_siswa }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $tanggal }}</div>
        </div>
    </div>

    <!-- Surah yang Sudah Dihafalkan -->
    <h4 class="section-title">Surah yang Sudah Dihafalkan</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Surah</th>
                <th style="width: 10%;">Sesi</th>
                <th style="width: 12%;">Tajwid</th>
                <th style="width: 12%;">Kelancaran</th>
                <th style="width: 12%;">Makhroj</th>
                <th style="width: 12%;">Nilai Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($surah_dihafalkan as $index => $surah)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $surah['nama_surah'] }}</td>
                    <td style="text-align: center;">{{ $surah['jumlah_sesi'] }}</td>
                    <td style="text-align: center;">{{ $surah['nilai_tajwid'] }}</td>
                    <td style="text-align: center;">{{ $surah['nilai_kelancaran'] }}</td>
                    <td style="text-align: center;">{{ $surah['nilai_makhroj'] }}</td>
                    <td style="text-align: center;"><b>{{ $surah['nilai_rata'] }}</b></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada hafalan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Target Hafalan yang Belum Dihafalkan -->
    @if(count($surah_belum_dihafalkan) > 0)
        <h4 class="section-title">Target Hafalan yang Belum Dihafalkan</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Surah</th>
                    <th style="width: 30%;">Status</th>
                    <th style="width: 30%;">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($surah_belum_dihafalkan as $index => $surah)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $surah['nama_surah'] }}</td>
                        <td>{{ $surah['status'] }}</td>
                        <td>{{ $surah['progress'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Halaman 1 dari 1</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
