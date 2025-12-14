<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Carbon\Carbon;

class LaporanHafalan extends Component
{
    public $kelasList = [];
    public $selectedKelasId = null;
    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null; // Data untuk popup
    public $detailLaporan = null;
    public $kelasDetail = null;
    public $siswaDetail = null;
    public $surahDetail = null;
    public $tanggalMulai = null;
    public $tanggalAkhir = null;

    // PROPERTI PAGINATION
    public $currentPageSesi = 1;
    public $perPageSesi = 10;
    public $totalPagesSesi = 1;

    public $currentPageSurahSelesai = 1;
    public $perPageSurahSelesai = 10;
    public $totalPagesSurahSelesai = 1;

    public $currentPageTarget = 1;
    public $perPageTarget = 10;
    public $totalPagesTarget = 1;

    public function mount()
    {
        $this->loadKelasList();
    }

    public function loadKelasList()
    {
        $this->kelasList = Kelas::with('siswa')
            ->get()
            ->map(function ($kelas) {
                $jumlahSiswa = $kelas->siswa->count();
                $tahunAjaran = $kelas->tahun_ajaran ?? 'Tidak Ada';

                return [
                    'id' => $kelas->id_kelas,
                    'nama_kelas' => $kelas->nama_kelas,
                    'tahun_ajaran' => $tahunAjaran,
                    'jumlah_siswa' => $jumlahSiswa,
                ];
            })
            ->toArray();
    }

    public function selectKelas($kelasId)
    {
        $this->selectedKelasId = $kelasId;
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
        $this->loadDetailLaporan();
    }

    public function loadDetailLaporan()
    {
        if (!$this->selectedKelasId) {
            $this->detailLaporan = null;
            $this->kelasDetail = null;
            return;
        }

        $kelas = Kelas::with(['siswa.kelompok'])->find($this->selectedKelasId);
        if (!$kelas) {
            return;
        }

        $this->kelasDetail = [
            'nama_kelas' => $kelas->nama_kelas,
            'tahun_ajaran' => $kelas->tahun_ajaran ?? 'Tidak Ada',
        ];

        $siswaDetail = $kelas->siswa->map(function ($siswa) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

            $kelompok = $siswa->kelompok->first();
            $namaKelompok = $kelompok ? $kelompok->nama_kelompok : '-';
            $target = $kelompok ? TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)->first() : null;

            $surahSelesaiCount = 0;
            $totalTargetSurah = 0;

            if ($target) {
                $totalTargetSurah = abs($target->id_surah_akhir - $target->id_surah_awal) + 1;
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $idSurah) {
                    $surah = Surah::find($idSurah);
                    if (!$surah)
                        continue;

                    $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->where(function ($q) use ($idSurah) {
                            $q->where('id_surah_mulai', $idSurah)->orWhere('id_surah_selesai', $idSurah);
                        })
                        ->orderByDesc('ayat_selesai')
                        ->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            $progressTarget = $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target";

            return [
                'id_siswa' => $siswa->id_siswa,
                'nama_siswa' => $siswa->nama_siswa,
                'nama_kelompok' => $namaKelompok,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata,
                'progress_target' => $progressTarget,
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();

        $this->detailLaporan = $siswaDetail;
    }

    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSurahId = null;
        $this->tanggalMulai = null;
        $this->tanggalAkhir = null;
        $this->currentPageSesi = 1;
        $this->currentPageSurahSelesai = 1;
        $this->currentPageTarget = 1;
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

        // --- 1. RIWAYAT SESI ---
        $query = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->with('surahMulai', 'surahSelesai');

        if ($this->tanggalMulai) {
            $query->whereDate('tanggal_setor', '>=', $this->tanggalMulai);
        }
        if ($this->tanggalAkhir) {
            $query->whereDate('tanggal_setor', '<=', $this->tanggalAkhir);
        }

        $allRiwayatSesi = $query->orderByDesc('tanggal_setor')->get();

        $jumlahSesi = $allRiwayatSesi->count();
        $nilaiTajwid = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('nilai_rata'), 2) : 0;

        $this->totalPagesSesi = ceil($jumlahSesi / $this->perPageSesi);
        $offsetSesi = ($this->currentPageSesi - 1) * $this->perPageSesi;
        $riwayatSesi = $allRiwayatSesi->slice($offsetSesi, $this->perPageSesi);

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
            ];
        })->values()->toArray();

        // --- 2. SURAH TUNTAS ---
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
                    'id_surah' => $idSurah,
                    'nama_surah' => $surah ? $surah->nama_surah : 'Unknown',
                    'nomor_surah' => $surah ? $surah->nomor_surah : 999,
                    'jumlah_ayat_surah' => $surah ? $surah->jumlah_ayat : 0,
                    'count' => 0,
                    'max_ayat' => 0,
                    'latest_scores' => []
                ];
            }

            $statsSurah[$idSurah]['count']++;
            if ($sesi->ayat_selesai > $statsSurah[$idSurah]['max_ayat']) {
                $statsSurah[$idSurah]['max_ayat'] = $sesi->ayat_selesai;
            }
            $statsSurah[$idSurah]['latest_scores'] = [
                'tajwid' => $sesi->skor_tajwid,
                'kelancaran' => $sesi->skor_kelancaran,
                'makhroj' => $sesi->skor_makhroj,
                'rata_rata' => $sesi->nilai_rata
            ];
        }

        $allSurahSelesai = [];
        foreach ($statsSurah as $stat) {
            if ($stat['max_ayat'] >= $stat['jumlah_ayat_surah']) {
                $allSurahSelesai[] = [
                    'id_surah' => $stat['id_surah'],
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

        usort($allSurahSelesai, function ($a, $b) {
            return $a['nomor_surah'] <=> $b['nomor_surah'];
        });

        $totalSurahSelesai = count($allSurahSelesai);
        $this->totalPagesSurahSelesai = ceil($totalSurahSelesai / $this->perPageSurahSelesai);
        $offsetSurahSelesai = ($this->currentPageSurahSelesai - 1) * $this->perPageSurahSelesai;
        $surahSudahDihafal = array_slice($allSurahSelesai, $offsetSurahSelesai, $this->perPageSurahSelesai);

        // --- 3. TARGET HAFALAN ---
        $siswaKelompok = $siswa->kelompok;
        $allTargetBelumDihafalkan = [];

        if ($siswaKelompok->isNotEmpty()) {
            $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
            $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

            foreach ($targetHafalan as $target) {
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));
                foreach ($rangeSurah as $i) {
                    $isTuntas = false;
                    if (isset($statsSurah[$i]) && $statsSurah[$i]['max_ayat'] >= $statsSurah[$i]['jumlah_ayat_surah']) {
                        $isTuntas = true;
                    }
                    if (!$isTuntas) {
                        $surah = Surah::find($i);
                        if ($surah) {
                            $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                                ->where(function ($q) use ($i) {
                                    $q->where('id_surah_mulai', $i)->orWhere('id_surah_selesai', $i);
                                })->orderByDesc('ayat_selesai')->first();

                            $status = $cekSesi ? 'Sedang Menghafal' : 'Belum Dimulai';
                            $progress = $cekSesi ? $cekSesi->ayat_selesai . '/' . $surah->jumlah_ayat . ' ayat' : '0/' . $surah->jumlah_ayat . ' ayat';

                            $allTargetBelumDihafalkan[] = [
                                'no' => $surah->nomor_surah,
                                'nama_surah' => $surah->nama_surah,
                                'jumlah_ayat' => $surah->jumlah_ayat,
                                'status' => $status,
                                'progress' => $progress,
                            ];
                        }
                    }
                }
            }
        }

        $totalTarget = count($allTargetBelumDihafalkan);
        $this->totalPagesTarget = ceil($totalTarget / $this->perPageTarget);
        $offsetTarget = ($this->currentPageTarget - 1) * $this->perPageTarget;
        $targetBelumDihafalkan = array_slice($allTargetBelumDihafalkan, $offsetTarget, $this->perPageTarget);

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

    // Pagination Methods
    public function nextPageSesi()
    {
        if ($this->currentPageSesi < $this->totalPagesSesi) {
            $this->currentPageSesi++;
            $this->loadDetailSiswa();
        }
    }
    public function prevPageSesi()
    {
        if ($this->currentPageSesi > 1) {
            $this->currentPageSesi--;
            $this->loadDetailSiswa();
        }
    }
    public function nextPageSurahSelesai()
    {
        if ($this->currentPageSurahSelesai < $this->totalPagesSurahSelesai) {
            $this->currentPageSurahSelesai++;
            $this->loadDetailSiswa();
        }
    }
    public function prevPageSurahSelesai()
    {
        if ($this->currentPageSurahSelesai > 1) {
            $this->currentPageSurahSelesai--;
            $this->loadDetailSiswa();
        }
    }
    public function nextPageTarget()
    {
        if ($this->currentPageTarget < $this->totalPagesTarget) {
            $this->currentPageTarget++;
            $this->loadDetailSiswa();
        }
    }
    public function prevPageTarget()
    {
        if ($this->currentPageTarget > 1) {
            $this->currentPageTarget--;
            $this->loadDetailSiswa();
        }
    }

    public function selectSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['guru.akun', 'koreksi.ayat'])->find($sesiId);
        if (!$sesi)
            return;

        $urutanSesi = SesiHafalan::where('id_siswa', $sesi->id_siswa)
            ->where(function ($q) use ($sesi) {
                $q->where('id_surah_mulai', $sesi->id_surah_mulai)
                    ->orWhere('id_surah_selesai', $sesi->id_surah_mulai);
            })
            ->where('tanggal_setor', '<=', $sesi->tanggal_setor)
            ->count();

        $koreksiFormatted = $sesi->koreksi->map(function ($k) use ($urutanSesi) {
            return [
                'lokasi' => 'Ayat ' . ($k->ayat ? $k->ayat->nomor_ayat : ($k->kata_ke ?? '?')),
                'sesi_ke' => $urutanSesi,
                'jenis_kesalahan' => $k->kategori_kesalahan ?? '-',
                'catatan' => $k->catatan ?? '-'
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
        $this->currentPageSesi = 1;
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

        if (!$siswa || !$surah)
            return;

        // Ambil riwayat sesi, diurutkan dari yang TERBARU ke TERLAMA
        $sesiSurah = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->where(function ($q) use ($surah) {
                $q->where('id_surah_mulai', $surah->id_surah)
                    ->orWhere('id_surah_selesai', $surah->id_surah);
            })
            ->with('surahMulai', 'surahSelesai')
            ->orderByDesc('tanggal_setor') // PENTING: Index 0 adalah sesi terbaru
            ->get();

        $sesiFormatted = $sesiSurah->map(fn($sesi) => [
            'id_sesi' => $sesi->id_sesi,
            'tanggal_setor' => $sesi->tanggal_setor->format('d F Y'),
            'ayat_mulai' => $sesi->ayat_mulai,
            'ayat_selesai' => $sesi->ayat_selesai,
            'skor_tajwid' => round($sesi->skor_tajwid, 2),
            'skor_kelancaran' => round($sesi->skor_kelancaran, 2),
            'skor_makhroj' => round($sesi->skor_makhroj, 2),
            'nilai_rata' => round($sesi->nilai_rata, 2),
        ])->toArray();

        // [PERBAIKAN]
        // Karena data sudah urut DESC (Terbaru -> Terlama), maka sesi terakhir adalah elemen PERTAMA.
        $sesiTerakhir = $sesiSurah->first();

        $this->surahDetail = [
            'nama_siswa' => $siswa->nama_siswa,
            'nama_surah' => $surah->nama_surah,
            'nomor_surah' => $surah->nomor_surah,
            'jumlah_ayat' => $surah->jumlah_ayat,

            // Ambil nilai dari sesi terbaru (elemen pertama)
            'nilai_tajwid' => $sesiTerakhir ? round($sesiTerakhir->skor_tajwid, 2) : 0,
            'nilai_kelancaran' => $sesiTerakhir ? round($sesiTerakhir->skor_kelancaran, 2) : 0,
            'nilai_makhroj' => $sesiTerakhir ? round($sesiTerakhir->skor_makhroj, 2) : 0,
            'nilai_rata_rata' => $sesiTerakhir ? round($sesiTerakhir->nilai_rata, 2) : 0,

            'sesi_formatnya' => $sesiFormatted,
        ];
    }

    public function backToList()
    {
        $this->selectedKelasId = null;
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
        $this->detailLaporan = null;
        $this->siswaDetail = null;
        $this->surahDetail = null;
    }

    public function backToKelas()
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
        if ($this->selectedSiswaId) {
            $this->loadDetailSiswa();
        }
    }

    public function downloadPdf()
    {
        if (!$this->selectedKelasId)
            return;
        return redirect()->route('export.laporan-hafalan.pdf', ['kelasId' => $this->selectedKelasId]);
    }

    public function downloadExcel()
    {
        if (!$this->selectedKelasId)
            return;
        return redirect()->route('export.laporan-hafalan.excel', ['kelasId' => $this->selectedKelasId]);
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

    // METHOD INI DIKEMBALIKAN UNTUK TAB "DETAIL SURAH"
    public function downloadPdfSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.pdf', [
            'siswaId' => $this->selectedSiswaId,
            'surahId' => $this->selectedSurahId
        ]));
    }

    // METHOD INI DIKEMBALIKAN UNTUK TAB "DETAIL SURAH"
    public function downloadExcelSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.excel', [
            'siswaId' => $this->selectedSiswaId,
            'surahId' => $this->selectedSurahId
        ]));
    }

    // METHOD BARU UNTUK POPUP
    public function downloadPdfDetailSesi()
    {
        if (!$this->selectedSesiDetail || !isset($this->selectedSesiDetail['id']))
            return;
        return redirect()->away(route('export.sesi-detail.pdf', ['sesiId' => $this->selectedSesiDetail['id']]));
    }

    // METHOD BARU UNTUK POPUP
    public function downloadExcelDetailSesi()
    {
        if (!$this->selectedSesiDetail || !isset($this->selectedSesiDetail['id']))
            return;
        return redirect()->away(route('export.sesi-detail.excel', ['sesiId' => $this->selectedSesiDetail['id']]));
    }

    public function render()
    {
        return view('livewire.admin.laporan-hafalan')->layout('layouts.app');
    }
}