<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hafalan Baru</title>
    <style>
        /* Tambahan style sederhana untuk tombol */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            color: #ffffff;
            background-color: #059669; /* Warna hijau */
            text-decoration: none;
            border-radius: 5px;
            font-family: sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <p>Assalamu'alaikum, Wr. Wb.</p>
    <p>Berikut kami sampaikan laporan setoran hafalan terbaru atas nama:</p>

    <p><strong>Nama Siswa:</strong> {{ $sesi->siswa->nama_siswa }}</p>
    <p><strong>Guru Penilai:</strong> {{ $sesi->guru->akun->nama_lengkap }}</p>
    <p><strong>Tanggal:</strong> {{ $sesi->tanggal_setor->format('d F Y') }}</p>
    
    <p><strong>Setoran:</strong> Surah {{ $sesi->surahMulai->nama_surah }} (Ayat {{ $sesi->ayat_mulai }} s/d {{ $sesi->ayat_selesai }})</p>
    
    <p><strong>Nilai Rata-rata:</strong> {{ $sesi->nilai_rata }}</p>

    <br>

    <p>
        <a href="#" class="btn">
            Buka Aplikasi Hafizuna
        </a>
    </p>

    <p>Silakan login ke aplikasi untuk melihat detail koreksi jika diperlukan.</p>
    <p>Terima kasih.</p>
</body>
</html>