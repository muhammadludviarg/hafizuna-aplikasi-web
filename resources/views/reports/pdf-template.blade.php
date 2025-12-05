<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hafalan {{ $siswa->nama }}</title>
    <!-- CSS Sederhana untuk PDF -->
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #006400; margin: 0; }
        .header p { margin: 0; font-size: 14px; }
        .info { margin-bottom: 20px; }
        .info p { margin: 3px 0; }
        h3 { font-size: 16px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; }
        .status-belum { color: #D9534F; }
    </style>
</head>
<body>
    
    <div class="header">
        {{-- Jika Anda punya logo di folder public, Anda bisa gunakan: --}}
        {{-- <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 80px; margin-bottom: 10px;"> --}}
        <h1>HAFIZUNA</h1>
        <p>SD Islam Al-Azhar 27 Cibinong Bogor</p>
        <h2>Laporan Hafalan Al-Qur'an</h2>
    </div>

    <div class="info">
        <p><strong>Nama Siswa:</strong> {{ $siswa->nama }}</p>
        <p><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ $tanggal }}</p>
    </div>

    <h3>Surah yang Sudah Dihafalkan</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Surah</th>
                <th>Sesi ke-</th>
                <th>Tajwid</th>
                <th>Kelancaran</th>
                <th>Makhroj</th>
                <th>Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sudahDihapal as $hafalan)
                <tr>
                    <td>{{ $hafalan->nama_surah }}</td>
                    <td>{{ $hafalan->total_sesi }}</td>
                    <td>{{ number_format($hafalan->avg_tajwid, 0) }}</td>
                    <td>{{ number_format($hafalan->avg_kelancaran, 0) }}</td>
                    <td>{{ number_format($hafalan->avg_makhroj, 0) }}</td>
                    <td><strong>{{ number_format($hafalan->avg_rata_rata, 0) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada hafalan yang dinilai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Target Hafalan yang Belum Dihafalkan</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Surah</th>
                <th>Status</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($belumDihapal as $target)
                <tr>
                    <td>{{ $target->nama_surah }}</td>
                    <td><span class="status-belum">Belum Dimulai</span></td>
                    <td>0/{{ $target->jumlah_ayat }} ayat</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Semua target hafalan telah diselesaikan!</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>