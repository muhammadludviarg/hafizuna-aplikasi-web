<!DOCTYPE html>
<html>

<head>
    <title>Laporan Hafalan Baru</title>
</head>

<body>
    <p>Assalamu'alaikum, Wr. Wb.</p>
    <p>Berikut kami sampaikan laporan setoran hafalan terbaru atas nama:</p>

    <p><strong>Nama Siswa:</strong> {{ $sesi->siswa->nama_siswa }}</p>
    <p><strong>Guru Penilai:</strong> {{ $sesi->guru->akun->nama_lengkap }}</p>
    <p><strong>Tanggal:</strong> {{ $sesi->tanggal_setor->format('d F Y') }}</p>
    <p><strong>Nilai Rata-rata:</strong> {{ $sesi->nilai_rata }}</p>

    <p>Silakan login ke aplikasi Hafizuna untuk melihat detail koreksi.</p>
    <p>Terima kasih.</p>
</body>

</html>