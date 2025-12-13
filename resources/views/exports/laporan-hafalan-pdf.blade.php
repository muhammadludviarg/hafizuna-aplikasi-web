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
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        td:nth-child(2) {
            text-align: left;
            /* Nama Siswa rata kiri */
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
    {{-- LOGIKA DETEKSI KOLOM KELOMPOK --}}
    @php
        $showKolomKelompok = false;
        if (!empty($siswa_data) && count($siswa_data) > 0) {
            if (array_key_exists('nama_kelompok', $siswa_data[0])) {
                $showKolomKelompok = true;
            }
        }
    @endphp

    <div class="header">
        <h1>{{ $sekolah }}</h1>
        <p>{{ $nama_sekolah_lengkap }} - {{ $lokasi }}</p>
    </div>

    <div class="info-section">
        <table style="border: none; margin-top: 0;">
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px; text-align: left; width: 150px; font-weight: bold;">Judul
                    Laporan:</td>
                <td style="border: none; padding: 2px; text-align: left;">{{ $judul }}</td>
            </tr>
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px; text-align: left; font-weight: bold;">Kelas/Kelompok:</td>
                {{-- Gunakan $nama_kelas jika ada, jika tidak (Guru) mungkin pakai nama kelompok atau string lain --}}
                <td style="border: none; padding: 2px; text-align: left;">{{ $nama_kelas ?? '-' }}</td>
            </tr>
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px; text-align: left; font-weight: bold;">Tahun Ajaran:</td>
                <td style="border: none; padding: 2px; text-align: left;">{{ $tahun_ajaran }}</td>
            </tr>
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px; text-align: left; font-weight: bold;">Tanggal Cetak:</td>
                <td style="border: none; padding: 2px; text-align: left;">{{ $tanggal }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                
                {{-- Lebar kolom Nama Siswa menyesuaikan: Jika Kelompok hilang, Nama Siswa jadi 40% --}}
                <th style="width: {{ $showKolomKelompok ? '25%' : '40%' }};">Nama Siswa</th>
                
                {{-- Hanya Tampilkan Header Kelompok Jika Admin --}}
                @if($showKolomKelompok)
                    <th style="width: 15%;">Kelompok</th> 
                @endif
                
                <th style="width: 25%;">Progress Target</th>
                <th style="width: 15%;">Jumlah Sesi</th>
                <th style="width: 15%;">Rata-rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa_data as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa['nama_siswa'] }}</td>
                    
                    {{-- Hanya Tampilkan Isi Kelompok Jika Admin --}}
                    @if($showKolomKelompok)
                        <td>{{ $siswa['nama_kelompok'] ?? '-' }}</td> 
                    @endif
                    
                    <td>{{ $siswa['progress_target'] }}</td>
                    <td>{{ $siswa['jumlah_sesi'] }}</td>
                    <td>{{ $siswa['nilai_rata_rata'] }}</td>
                </tr>
            @empty
                <tr>
                    {{-- Colspan menyesuaikan jumlah kolom aktif (6 atau 5) --}}
                    <td colspan="{{ $showKolomKelompok ? 6 : 5 }}" style="text-align: center;">Tidak ada data siswa</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh sistem Hafizuna</p>
    </div>
</body>

</html>