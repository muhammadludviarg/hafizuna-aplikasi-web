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
    public $kelompokList = [];
    public $selectedKelompokId = null;

    public $selectedKelasId = null; // Tetap ada meski tidak dipakai langsung di view, untuk hindari error
    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null;

    public $detailLaporan = null;
    public $kelompokDetail = null;
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
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            $this->kelompokList = [];
            return;
        }

        $this->kelompokList = Kelompok::with(['kelas'])
            ->where('id_guru', $guru->id_guru)
            ->get()
            ->map(function ($kelompok) {
                $jumlahSiswa = $kelompok->siswa->count();
                $namaKelas = $kelompok->kelas ? $kelompok->kelas->nama_kelas : 'Tanpa Kelas';

                return [
                    'id' => $kelompok->id_kelompok,
                    'nama_kelompok_utama' => $kelompok->nama_kelompok ?? 'Kelompok',
                    'nama_kelas_kecil' => $namaKelas,
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
            'nama_kelompok' => "Kelompok " . ($kelompok->nama_kelompok ?? '-') . " - " . ($kelompok->kelas->nama_kelas ?? '-'),
            'tahun_ajaran' => $kelompok->kelas->tahun_ajaran ?? 'Tidak Ada',
        ];

        // 1. Ambil Target Kelompok
        $target = TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)->first();
        $totalTargetSurah = 0;
        $rangeSurah = [];

        if ($target) {
            $totalTargetSurah = abs($target->id_surah_akhir - $target->id_surah_awal) + 1;
            $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));
        }

        $siswaDetail = $kelompok->siswa->map(function ($siswa) use ($kelompok, $target, $totalTargetSurah, $rangeSurah) {
            // Hitung surah yang SUDAH SELESAI
            $surahSelesaiCount = 0;

            if ($target) {
                // Logic: Cari sesi hafalan utk setiap surah target
                // Dianggap selesai jika ayat_selesai >= jumlah_ayat surah tersebut
                // Ini query-nya di dalam loop map siswa, hati-hati N+1. 
                // Tapi untuk skala kecil masih oke.

                $surahSelesaiCount = 0;
                foreach ($rangeSurah as $idSurah) {
                    $surah = Surah::find($idSurah);
                    if (!$surah)
                        continue;

                    $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->where(function ($q) use ($idSurah) {
                            $q->where('id_surah_mulai', $idSurah)
                                ->orWhere('id_surah_selesai', $idSurah);
                        })
                        ->orderByDesc('ayat_selesai') // Ambil yg progress terjauh
                        ->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

            return [
                'id_siswa' => $siswa->id_siswa,
                'nama_siswa' => $siswa->nama_siswa,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
                'progress_target' => $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target",
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();

        $this->detailLaporan = $siswaDetail;
    }

    // ... (Sisa method selectSiswa, loadDetailSiswa, dll SAMA SEPERTI SEBELUMNYA) ...
    // HANYA PERLU UPDATE loadDetailSiswa untuk logika target 'Sedang Menghafal'

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
        if (!$siswa)
            return;

        // --- 1. LOGIKA RIWAYAT SESI (Terapkan Filter Tanggal) ---
        $query = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->with('surahMulai', 'surahSelesai');

        if ($this->tanggalMulai)
            $query->whereDate('tanggal_setor', '>=', $this->tanggalMulai);
        if ($this->tanggalAkhir)
            $query->whereDate('tanggal_setor', '<=', $this->tanggalAkhir);

        $riwayatSesi = $query->orderByDesc('tanggal_setor')->get();

        // Statistik Filtered
        $jumlahSesi = $riwayatSesi->count();
        $nilaiTajwid = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesi > 0 ? round($riwayatSesi->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesi > 0 ? round($riwayatSesi->avg('nilai_rata'), 2) : 0;

        $riwayatFormatted = $riwayatSesi->map(function ($sesi) {
            // ... (kode mapping riwayat sesi tetap sama seperti sebelumnya) ...
            $surahMulai = $sesi->surahMulai;
            $surahSelesai = $sesi->surahSelesai;
            $surahText = $surahMulai->nama_surah . ($surahMulai->id_surah !== $surahSelesai->id_surah ? ' - ' . $surahSelesai->nama_surah : '');

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
            ];
        })->toArray();

        // --- 2. LOGIKA SURAH SUDAH DIHAFAL (Mirip Logic Export PDF) ---
        // Ambil SEMUA sesi (tanpa filter tanggal) urut dari lama ke baru
        $allSesi = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->with('surahMulai')
            ->orderBy('tanggal_setor', 'asc')
            ->get();

        $statsSurah = [];

        foreach ($allSesi as $sesi) {
            $idSurah = $sesi->id_surah_mulai;
            $surah = $sesi->surahMulai;

            if (!isset($statsSurah[$idSurah])) {
                $statsSurah[$idSurah] = [
                    'nama_surah' => $surah ? $surah->nama_surah : 'Unknown',
                    'nomor_surah' => $surah ? $surah->nomor_surah : 999,
                    'jumlah_ayat_surah' => $surah ? $surah->jumlah_ayat : 0,
                    'count' => 0,
                    'max_ayat' => 0,
                    'latest_scores' => []
                ];
            }

            // Update data akumulatif
            $statsSurah[$idSurah]['count']++; // Jumlah pertemuan

            // Update progress terjauh
            if ($sesi->ayat_selesai > $statsSurah[$idSurah]['max_ayat']) {
                $statsSurah[$idSurah]['max_ayat'] = $sesi->ayat_selesai;
            }

            // Overwrite dengan nilai sesi terbaru (karena loop ASC, yg terakhir pasti terbaru)
            $statsSurah[$idSurah]['latest_scores'] = [
                'tajwid' => $sesi->skor_tajwid,
                'kelancaran' => $sesi->skor_kelancaran,
                'makhroj' => $sesi->skor_makhroj,
                'rata_rata' => $sesi->nilai_rata
            ];
        }

        // Filter hanya yang sudah TUNTAS (max_ayat >= jumlah_ayat_surah)
        $surahSudahDihafal = [];
        foreach ($statsSurah as $stat) {
            if ($stat['max_ayat'] >= $stat['jumlah_ayat_surah']) {
                $surahSudahDihafal[] = [
                    'nama_surah' => $stat['nama_surah'],
                    'nomor_surah' => $stat['nomor_surah'],
                    'jumlah_sesi' => $stat['count'],
                    'nilai_tajwid' => $stat['latest_scores']['tajwid'],
                    'nilai_kelancaran' => $stat['latest_scores']['kelancaran'],
                    'nilai_makhroj' => $stat['latest_scores']['makhroj'],
                    'nilai_rata' => $stat['latest_scores']['rata_rata'],
                ];
            }
        }

        // Urutkan berdasarkan nomor surah (opsional, biar rapi)
        usort($surahSudahDihafal, function ($a, $b) {
            return $a['nomor_surah'] <=> $b['nomor_surah'];
        });

        // TARGET HAFALAN
        $siswaKelompok = $siswa->kelompok;
        $targetBelumDihafalkan = [];

        if ($siswaKelompok->isNotEmpty()) {
            $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
            $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

            foreach ($targetHafalan as $target) {
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $i) {
                    $surah = Surah::find($i);
                    if ($surah) {
                        // Cek status hafalan
                        $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                            ->where(function ($q) use ($i) {
                                $q->where('id_surah_mulai', $i)
                                    ->orWhere('id_surah_selesai', $i);
                            })
                            ->orderByDesc('ayat_selesai')
                            ->first();

                        $status = 'Belum Dimulai';
                        $progress = '0 / ' . $surah->jumlah_ayat . ' ayat';
                        $isTuntas = false;

                        if ($cekSesi) {
                            if ($cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                                $isTuntas = true;
                            } else {
                                $status = 'Sedang Menghafal';
                                $progress = $cekSesi->ayat_selesai . ' / ' . $surah->jumlah_ayat . ' ayat';
                            }
                        }

                        if (!$isTuntas) {
                            $targetBelumDihafalkan[] = [
                                'no' => $surah->nomor_surah,
                                'nama_surah' => $surah->nama_surah,
                                'status' => $status,
                                'progress' => $progress,
                            ];
                        }
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
            'surah_sudah_dihafal' => $surahSudahDihafal,
        ];
    }

    public function selectSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['guru.akun', 'koreksi.ayat'])->find($sesiId);
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
            'guru' => ($sesi->guru && $sesi->guru->akun) ? $sesi->guru->akun->nama_lengkap : 'Belum ditentukan',
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
        if (!$this->selectedKelompokId) {
            session()->flash('error', 'Pilih kelompok terlebih dahulu');
            return;
        }
        return redirect()->away(route('export.laporan-hafalan.pdf-kelompok', ['kelompokId' => $this->selectedKelompokId]));
    }

    public function downloadExcel()
    {
        if (!$this->selectedKelompokId) {
            session()->flash('error', 'Pilih kelompok terlebih dahulu');
            return;
        }
        return redirect()->away(route('export.laporan-hafalan.excel-kelompok', ['kelompokId' => $this->selectedKelompokId]));
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
