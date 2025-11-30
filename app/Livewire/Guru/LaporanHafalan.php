<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\Auth;

class LaporanHafalan extends Component
{
    // Ganti 'kelasList' jadi 'kelompokList' biar lebih relevan
    public $kelompokList = [];
    public $selectedKelompokId = null;

    // Properti lainnya sama seperti Admin
    public $selectedKelasId = null;
    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null;

    public $detailLaporan = null; // Ini untuk daftar siswa
    public $kelompokDetail = null; // Ganti kelasDetail jadi kelompokDetail
    public $siswaDetail = null;
    public $surahDetail = null;

    public $tanggalMulai = null;
    public $tanggalAkhir = null;

    public function mount()
    {
        $this->loadKelompokList();
    }

    public function loadKelompokList()
    {
        // AMBIL DATA GURU YANG SEDANG LOGIN
        $user = Auth::user();
        $guru = $user->guru; // Relasi user ke guru

        if (!$guru) {
            $this->kelompokList = [];
            return;
        }

        // AMBIL KELOMPOK YANG DIAJAR GURU INI SAJA
        $this->kelompokList = Kelompok::with(['kelas'])
            ->where('id_guru', $guru->id_guru)
            ->get()
            ->map(function ($kelompok) {
                $jumlahSiswa = $kelompok->siswa->count();
                $namaKelas = $kelompok->kelas ? $kelompok->kelas->nama_kelas : 'Tanpa Kelas';

                return [
                    'id' => $kelompok->id_kelompok,
                    // Tampilkan Nama Kelas sebagai identitas kelompok
                    'nama_kelompok' => "Kelompok " . $namaKelas,
                    'tahun_ajaran' => $kelompok->tahun_ajaran ?? 'Tidak Ada',
                    'jumlah_siswa' => $jumlahSiswa,
                ];
            })
            ->toArray();
    }

    public function selectKelompok($kelompokId)
    {
        $this->selectedKelompokId = $kelompokId;
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
        $this->loadDetailLaporan();
    }

    public function loadDetailLaporan()
    {
        if (!$this->selectedKelompokId) {
            $this->detailLaporan = null;
            $this->kelompokDetail = null;
            return;
        }

        $kelompok = Kelompok::with(['kelas', 'siswa'])->find($this->selectedKelompokId);
        if (!$kelompok)
            return;

        $this->kelompokDetail = [
            'nama_kelompok' => "Kelompok " . ($kelompok->kelas->nama_kelas ?? '-'),
            'tahun_ajaran' => $kelompok->tahun_ajaran ?? 'Tidak Ada',
        ];

        // Ambil Siswa HANYA dari kelompok ini
        $siswaDetail = $kelompok->siswa->map(function ($siswa) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();

            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0
                ? round($sesiHafalan->avg('nilai_rata'), 2)
                : 0;

            $totalAyat = 0;
            foreach ($sesiHafalan as $sesi) {
                $totalAyat += ($sesi->ayat_selesai - $sesi->ayat_mulai + 1);
            }

            return [
                'id_siswa' => $siswa->id_siswa,
                'nama_siswa' => $siswa->nama_siswa,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
                'total_ayat' => $totalAyat,
            ];
        })->sortByDesc('nilai_rata_rata')
            ->values()
            ->toArray();

        $this->detailLaporan = $siswaDetail;
    }

    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSurahId = null;
        $this->tanggalMulai = null;
        $this->tanggalAkhir = null;
        $this->loadDetailSiswa();
    }

    public function loadDetailSiswa()
    {
        if (!$this->selectedSiswaId) {
            $this->siswaDetail = null;
            return;
        }

        $siswa = Siswa::find($this->selectedSiswaId);
        if (!$siswa) {
            return;
        }

        $query = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->with('surahMulai', 'surahSelesai');

        // Filter berdasarkan tanggal jika diisi
        if ($this->tanggalMulai) {
            $query->where('tanggal_setor', '>=', $this->tanggalMulai);
        }
        if ($this->tanggalAkhir) {
            $query->where('tanggal_setor', '<=', $this->tanggalAkhir);
        }

        $riwayatSesi = $query->orderByDesc('tanggal_setor')->get();

        // Hitung statistik
        $jumlahSesi = $riwayatSesi->count();
        $nilaiTajwid = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesi > 0 ? round($riwayatSesi->avg('nilai_rata'), 2) : 0;

        // Format riwayat sesi untuk ditampilkan
        $riwayatFormatted = $riwayatSesi->map(function ($sesi) {
            $surahMulai = $sesi->surahMulai;
            $surahSelesai = $sesi->surahSelesai;

            $surahText = $surahMulai->nama_surah;
            if ($surahMulai->id_surah !== $surahSelesai->id_surah) {
                $surahText .= ' - ' . $surahSelesai->nama_surah;
            }

            return [
                'id_sesi' => $sesi->id_sesi,
                'tanggal_setor' => $sesi->tanggal_setor->format('d F Y'),
                'surah_text' => $surahText,
                'ayat_mulai' => $sesi->ayat_mulai,
                'ayat_selesai' => $sesi->ayat_selesai,
                'skor_tajwid' => $sesi->skor_tajwid,
                'skor_kelancaran' => $sesi->skor_kelancaran,
                'skor_makhroj' => $sesi->skor_makhroj,
                'nilai_rata' => $sesi->nilai_rata,
                'id_surah_mulai' => $surahMulai->id_surah,
                'nama_surah_mulai' => $surahMulai->nama_surah,
                'nomor_surah_mulai' => $surahMulai->nomor_surah,
            ];
        })->toArray();

        $surahYaHafalIdsArray = $riwayatSesi->flatMap(function ($sesi) {
            return [$sesi->id_surah_mulai, $sesi->id_surah_selesai];
        })->unique()->values()->toArray();

        $siswaKelompok = $siswa->kelompok;
        $targetHafalan = [];

        if ($siswaKelompok->isNotEmpty()) {
            // Get target hafalan from all kelompok that this siswa belongs to
            $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
            $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();
        }

        $targetBelumDihafalkan = [];
        foreach ($targetHafalan as $target) {
            // Check range dari id_surah_awal to id_surah_akhir
            $surahAwal = $target->id_surah_awal;
            $surahAkhir = $target->id_surah_akhir;

            // SOLUSI: Gunakan range() agar bisa membaca urutan mundur (misal 114 ke 78)
            $listSurahTarget = range($surahAwal, $surahAkhir);

            foreach ($listSurahTarget as $i) {
                if (!in_array($i, $surahYaHafalIdsArray)) {
                    $surah = Surah::find($i);
                    if ($surah) {
                        $targetBelumDihafalkan[] = [
                            'no' => $surah->nomor_surah,
                            'nama_surah' => $surah->nama_surah,
                            'jumlah_ayat' => $surah->jumlah_ayat,
                            'status' => 'Belum Dimulai',
                            'progress' => '0/' . $surah->jumlah_ayat . ' ayat',
                        ];
                    }
                }
            }
        }

        $this->siswaDetail = [
            'nama_siswa' => $siswa->nama_siswa,
            'jumlah_sesi' => $jumlahSesi,
            'nilai_tajwid' => $nilaiTajwid,
            'nilai_kelancaran' => $nilaiKelancaran,
            'nilai_makhroj' => $nilaiMakhroj,
            'nilai_rata_rata' => $nilaiRataRata,
            'riwayat_sesi' => $riwayatFormatted,
            'target_belum_dihafalkan' => $targetBelumDihafalkan,
        ];
    }

    public function selectSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['guru', 'koreksi.ayat'])->find($sesiId);
        if (!$sesi)
            return;

        // Hitung Sesi Ke Berapa (Logic Kumulatif)
        // Hitung jumlah sesi siswa ini, surah ini, yang tanggalnya <= sesi ini
        $urutanSesi = SesiHafalan::where('id_siswa', $sesi->id_siswa)
            ->where(function ($q) use ($sesi) {
                $q->where('id_surah_mulai', $sesi->id_surah_mulai)
                    ->orWhere('id_surah_selesai', $sesi->id_surah_mulai);
            })
            ->where('tanggal_setor', '<=', $sesi->tanggal_setor)
            ->count();

        // Format data koreksi
        $koreksiFormatted = $sesi->koreksi->map(function ($k) use ($urutanSesi) {
            return [
                'lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')),
                'sesi_ke' => $urutanSesi, // Ini kolom baru yang diminta
                'jenis_kesalahan' => $k->kategori_kesalahan ?? '-',
                'catatan' => $k->catatan ?? '-' // Arab di UI aman tanpa reshaping
            ];
        })->toArray();

        $this->selectedSesiDetail = [
            'id' => $sesi->id_sesi,
            'tanggal' => \Carbon\Carbon::parse($sesi->tanggal_setor)->translatedFormat('d F Y'),
            'guru' => $sesi->guru ? $sesi->guru->nama_guru : 'Belum ditentukan',
            'ayat_mulai' => $sesi->ayat_mulai,
            'ayat_selesai' => $sesi->ayat_selesai,
            'nilai_tajwid' => $sesi->skor_tajwid,
            'nilai_kelancaran' => $sesi->skor_kelancaran,
            'nilai_makhroj' => $sesi->skor_makhroj,
            'nilai_rata' => $sesi->nilai_rata,
            'koreksi' => $koreksiFormatted
        ];
    }

    public function closeSesiDetail()
    {
        $this->selectedSesiDetail = null;
    }

    public function filterPeriode()
    {
        $this->loadDetailSiswa();
    }

    public function selectSurah($surahId)
    {
        $this->selectedSurahId = $surahId;
        $this->loadDetailSurah();
    }

    public function loadDetailSurah()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId) {
            $this->surahDetail = null;
            return;
        }

        $siswa = Siswa::find($this->selectedSiswaId);
        $surah = Surah::find($this->selectedSurahId);

        if (!$siswa || !$surah) {
            return;
        }

        $sesiSurah = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->where(function ($q) use ($surah) {
                $q->where('id_surah_mulai', $surah->id_surah)
                    ->orWhere('id_surah_selesai', $surah->id_surah);
            })
            ->with('surahMulai', 'surahSelesai')
            ->orderByDesc('tanggal_setor')
            ->get();

        // Format data sesi untuk surah ini
        $sesiFormatted = $sesiSurah->map(function ($sesi) {
            return [
                'id_sesi' => $sesi->id_sesi,
                'tanggal_setor' => $sesi->tanggal_setor->format('d F Y'),
                'ayat_mulai' => $sesi->ayat_mulai,
                'ayat_selesai' => $sesi->ayat_selesai,
                'skor_tajwid' => $sesi->skor_tajwid,
                'skor_kelancaran' => $sesi->skor_kelancaran,
                'skor_makhroj' => $sesi->skor_makhroj,
                'nilai_rata' => $sesi->nilai_rata,
            ];
        })->toArray();

        // Hitung statistik untuk surah ini
        $jumlahSesiSurah = count($sesiFormatted);
        $nilaiTajwid = $jumlahSesiSurah > 0 ? round($sesiSurah->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesiSurah > 0 ? round($sesiSurah->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesiSurah > 0 ? round($sesiSurah->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesiSurah > 0 ? round($sesiSurah->avg('nilai_rata'), 2) : 0;

        $this->surahDetail = [
            'nama_siswa' => $siswa->nama_siswa,
            'nama_surah' => $surah->nama_surah,
            'nomor_surah' => $surah->nomor_surah,
            'jumlah_ayat' => $surah->jumlah_ayat,
            'jumlah_sesi_surah' => $jumlahSesiSurah,
            'nilai_tajwid' => $nilaiTajwid,
            'nilai_kelancaran' => $nilaiKelancaran,
            'nilai_makhroj' => $nilaiMakhroj,
            'nilai_rata_rata' => $nilaiRataRata,
            'sesi_formatnya' => $sesiFormatted,
        ];
    }


    // --- NAVIGATION ---
    public function backToList()
    {
        $this->selectedKelompokId = null; // Reset Kelompok
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
        $this->detailLaporan = null;
        $this->siswaDetail = null;
        $this->surahDetail = null;
    }

    public function backToKelas() // Ini sebenarnya backToKelompok
    {
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
        $this->siswaDetail = null;
        $this->surahDetail = null;
    }

    public function backToSiswa()
    {
        $this->selectedSurahId = null;
        $this->surahDetail = null;
        if ($this->selectedSiswaId)
            $this->loadDetailSiswa();
    }

    // --- DOWNLOAD ---
    // Sekarang menggunakan route umum (tanpa prefix 'admin.')

    // TAMBAHKAN METHOD INI (Untuk Download Laporan Kelas)
    public function downloadPdf()
    {
        if ($this->selectedKelompokId) {
            $kelompok = Kelompok::find($this->selectedKelompokId);
            if ($kelompok && $kelompok->id_kelas) {
                return redirect()->route('export.laporan-hafalan.pdf', ['kelasId' => $kelompok->id_kelas]);
            }
        }
    }

    public function downloadExcel()
    {
        if ($this->selectedKelompokId) {
            $kelompok = Kelompok::find($this->selectedKelompokId);
            if ($kelompok && $kelompok->id_kelas) {
                return redirect()->route('export.laporan-hafalan.excel', ['kelasId' => $kelompok->id_kelas]);
            }
        }
    }

    public function downloadPdfSiswa()
    {
        if (!$this->selectedSiswaId)
            return;
        return redirect()->away(route('export.laporan-hafalan.pdf-siswa', ['siswaId' => $this->selectedSiswaId]));
    }

    public function downloadExcelSiswa()
    {
        if (!$this->selectedSiswaId)
            return;
        return redirect()->away(route('export.laporan-hafalan.excel-siswa', ['siswaId' => $this->selectedSiswaId]));
    }

    public function downloadPdfSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.pdf', [
            'siswaId' => $this->selectedSiswaId,
            'surahId' => $this->selectedSurahId
        ]));
    }

    public function downloadExcelSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.excel', [
            'siswaId' => $this->selectedSiswaId,
            'surahId' => $this->selectedSurahId
        ]));
    }

    public function render()
    {
        return view('livewire.guru.laporan-hafalan')->layout('layouts.guru');
    }
}