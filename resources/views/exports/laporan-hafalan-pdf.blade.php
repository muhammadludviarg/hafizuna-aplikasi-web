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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        
        tbody tr:hover {
            background-color: #e8f5e9;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        
        .page-number {
            text-align: center;
            margin-top: 15px;
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
        <div class="info-row">
            <div class="info-label">Judul Laporan:</div>
            <div class="info-value">{{ $judul }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kelas:</div>
            <div class="info-value">{{ $nama_kelas }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tahun Ajaran:</div>
            <div class="info-value">{{ $tahun_ajaran }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $tanggal }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jumlah Siswa:</div>
            <div class="info-value">{{ $jumlah_siswa }} siswa</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Siswa</th>
                <th style="width: 20%;">Surah Selesai</th>
                <th style="width: 20%;">Total Sesi</th>
                <th style="width: 20%;">Rata-rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa_data as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa['nama_siswa'] }}</td>
                    <td>{{ $siswa['total_ayat'] }}</td>
                    <td>{{ $siswa['jumlah_sesi'] }}</td>
                    <td>{{ $siswa['nilai_rata_rata'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data siswa</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-number">
        Halaman 1 dari 1
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
