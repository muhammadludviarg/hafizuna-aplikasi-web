<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanHafalan extends Component
{
    public $kelompokList = [];
    public $selectedKelompokId = null;
    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null;
    public $detailLaporan = null;
    public $kelompokDetail = null;
    public $siswaDetail = null;
    public $surahDetail = null;
    public $tanggalMulai = null;
    public $tanggalAkhir = null;

    // PAGINATION
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
        $this->loadKelompokList();
    }

    public function loadKelompokList()
    {
        $guru = Auth::user()->guru;
        if (!$guru)
            return;

        $this->kelompokList = Kelompok::where('id_guru', $guru->id_guru)
            ->with('kelas')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id_kelompok,
                    'nama_kelompok' => $k->nama_kelompok . ' (' . ($k->kelas->nama_kelas ?? '-') . ')',
                    'jumlah_siswa' => $k->siswa()->count(),
                ];
            })->toArray();
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
            return;
        }

        $kelompok = Kelompok::with(['siswa', 'kelas'])->find($this->selectedKelompokId);
        if (!$kelompok)
            return;

        $this->kelompokDetail = [
            'nama_kelompok' => $kelompok->nama_kelompok,
            'nama_kelas' => $kelompok->kelas->nama_kelas ?? '-',
        ];

        $target = TargetHafalanKelompok::where('id_kelompok', $this->selectedKelompokId)->first();

        $this->detailLaporan = $kelompok->siswa->map(function ($siswa) use ($target) {
            $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
            $jumlahSesi = $sesiHafalan->count();
            $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

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
                        })->orderByDesc('ayat_selesai')->first();

                    if ($cekSesi && $cekSesi->ayat_selesai >= $surah->jumlah_ayat) {
                        $surahSelesaiCount++;
                    }
                }
            }

            return [
                'id_siswa' => $siswa->id_siswa,
                'nama_siswa' => $siswa->nama_siswa,
                'jumlah_sesi' => $jumlahSesi,
                'nilai_rata_rata' => $nilaiRataRata, // Sudah di-round di atas
                'progress_target' => $target ? "$surahSelesaiCount / $totalTargetSurah Surah" : "Belum ada target",
            ];
        })->sortByDesc('nilai_rata_rata')->values()->toArray();
    }

    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSurahId = null;
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
        $query = SesiHafalan::where('id_siswa', $this->selectedSiswaId)->with('surahMulai', 'surahSelesai');
        if ($this->tanggalMulai)
            $query->whereDate('tanggal_setor', '>=', $this->tanggalMulai);
        if ($this->tanggalAkhir)
            $query->whereDate('tanggal_setor', '<=', $this->tanggalAkhir);
        $allRiwayatSesi = $query->orderByDesc('tanggal_setor')->get();

        $jumlahSesi = $allRiwayatSesi->count();
        $nilaiTajwid = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('nilai_rata'), 2) : 0;

        $this->totalPagesSesi = ceil($jumlahSesi / $this->perPageSesi);
        $offsetSesi = ($this->currentPageSesi - 1) * $this->perPageSesi;
        $riwayatSesi = $allRiwayatSesi->slice($offsetSesi, $this->perPageSesi);

        // PERBAIKAN 1: Rounding di list riwayat sesi
        $riwayatFormatted = $riwayatSesi->map(function ($sesi) {
            $surahText = $sesi->surahMulai->nama_surah;
            if ($sesi->surahMulai->id_surah !== $sesi->surahSelesai->id_surah)
                $surahText .= ' - ' . $sesi->surahSelesai->nama_surah;
            return [
                'id_sesi' => $sesi->id_sesi,
                'tanggal_setor' => $sesi->tanggal_setor->format('d F Y'),
                'surah_text' => $surahText,
                'ayat_mulai' => $sesi->ayat_mulai,
                'ayat_selesai' => $sesi->ayat_selesai,
                'skor_tajwid' => round($sesi->skor_tajwid, 2),        // Rounding
                'skor_kelancaran' => round($sesi->skor_kelancaran, 2),// Rounding
                'skor_makhroj' => round($sesi->skor_makhroj, 2),      // Rounding
                'nilai_rata' => round($sesi->nilai_rata, 2),          // Rounding
                'id_surah_mulai' => $sesi->id_surah_mulai,
            ];
        })->values()->toArray();

        // --- 2. SURAH TUNTAS ---
        $allSesi = SesiHafalan::where('id_siswa', $this->selectedSiswaId)->with('surahMulai')->orderBy('tanggal_setor', 'asc')->get();
        $statsSurah = [];
        foreach ($allSesi as $sesi) {
            $idSurah = $sesi->id_surah_mulai;
            if (!isset($statsSurah[$idSurah])) {
                $statsSurah[$idSurah] = [
                    'id_surah' => $idSurah,
                    'nama_surah' => $sesi->surahMulai->nama_surah ?? 'Unknown',
                    'nomor_surah' => $sesi->surahMulai->nomor_surah ?? 999,
                    'jumlah_ayat_surah' => $sesi->surahMulai->jumlah_ayat ?? 0,
                    'count' => 0,
                    'max_ayat' => 0,
                    'latest_scores' => []
                ];
            }
            $statsSurah[$idSurah]['count']++;
            if ($sesi->ayat_selesai > $statsSurah[$idSurah]['max_ayat'])
                $statsSurah[$idSurah]['max_ayat'] = $sesi->ayat_selesai;

            // PERBAIKAN 2: Rounding di statistik surah (nilai terakhir)
            $statsSurah[$idSurah]['latest_scores'] = [
                'tajwid' => round($sesi->skor_tajwid, 2),
                'kelancaran' => round($sesi->skor_kelancaran, 2),
                'makhroj' => round($sesi->skor_makhroj, 2),
                'rata_rata' => round($sesi->nilai_rata, 2)
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
        usort($allSurahSelesai, fn($a, $b) => $a['nomor_surah'] <=> $b['nomor_surah']);

        $this->totalPagesSurahSelesai = ceil(count($allSurahSelesai) / $this->perPageSurahSelesai);
        $offsetSurahSelesai = ($this->currentPageSurahSelesai - 1) * $this->perPageSurahSelesai;
        $surahSudahDihafal = array_slice($allSurahSelesai, $offsetSurahSelesai, $this->perPageSurahSelesai);

        // --- 3. TARGET ---
        $siswaKelompok = $siswa->kelompok;
        $allTargetBelumDihafalkan = [];
        if ($siswaKelompok->isNotEmpty()) {
            $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
            $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();
            foreach ($targetHafalan as $target) {
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));
                foreach ($rangeSurah as $i) {
                    if (isset($statsSurah[$i]) && $statsSurah[$i]['max_ayat'] >= $statsSurah[$i]['jumlah_ayat_surah'])
                        continue;
                    $surah = Surah::find($i);
                    if ($surah) {
                        $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)->where(function ($q) use ($i) {
                            $q->where('id_surah_mulai', $i)->orWhere('id_surah_selesai', $i); })->orderByDesc('ayat_selesai')->first();
                        $status = $cekSesi ? 'Sedang Menghafal' : 'Belum Dimulai';
                        $progress = $cekSesi ? $cekSesi->ayat_selesai . '/' . $surah->jumlah_ayat . ' ayat' : '0/' . $surah->jumlah_ayat . ' ayat';
                        $allTargetBelumDihafalkan[] = ['no' => $surah->nomor_surah, 'nama_surah' => $surah->nama_surah, 'status' => $status, 'progress' => $progress];
                    }
                }
            }
        }
        $this->totalPagesTarget = ceil(count($allTargetBelumDihafalkan) / $this->perPageTarget);
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

    // PAGINATION NAVIGATORS
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

    public function selectSurah($surahId)
    {
        $this->selectedSurahId = $surahId;
        $this->loadDetailSurah();
    }

    public function loadDetailSurah()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        $siswa = Siswa::find($this->selectedSiswaId);
        $surah = Surah::find($this->selectedSurahId);
        $sesiSurah = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->where(function ($q) use ($surah) {
                $q->where('id_surah_mulai', $surah->id_surah)->orWhere('id_surah_selesai', $surah->id_surah); })
            ->with('surahMulai')->orderByDesc('tanggal_setor')->get();

        // PERBAIKAN 3: Rounding di detail surah (tabel list sesi)
        $sesiFormatted = $sesiSurah->map(fn($sesi) => [
            'id_sesi' => $sesi->id_sesi,
            'tanggal_setor' => $sesi->tanggal_setor->format('d F Y'),
            'ayat_mulai' => $sesi->ayat_mulai,
            'ayat_selesai' => $sesi->ayat_selesai,
            'nilai_tajwid' => round($sesi->skor_tajwid, 2),
            'nilai_kelancaran' => round($sesi->skor_kelancaran, 2),
            'nilai_makhroj' => round($sesi->skor_makhroj, 2),
            'nilai_rata' => round($sesi->nilai_rata, 2),
            'id_surah_mulai' => $sesi->id_surah_mulai, // Tambahan ID Surah
        ])->toArray();

        $this->surahDetail = [
            'nama_siswa' => $siswa->nama_siswa,
            'nama_surah' => $surah->nama_surah,
            'nomor_surah' => $surah->nomor_surah,
            'jumlah_ayat' => $surah->jumlah_ayat,
            'nilai_tajwid' => round($sesiSurah->avg('skor_tajwid'), 2),
            'nilai_kelancaran' => round($sesiSurah->avg('skor_kelancaran'), 2),
            'nilai_makhroj' => round($sesiSurah->avg('skor_makhroj'), 2),
            'nilai_rata_rata' => round($sesiSurah->avg('nilai_rata'), 2),
            'sesi_formatnya' => $sesiFormatted
        ];
    }

    public function selectSesi($sesiId)
    {
        $sesi = SesiHafalan::with(['guru.akun', 'koreksi.ayat'])->find($sesiId);
        if (!$sesi)
            return;
        $urutan = SesiHafalan::where('id_siswa', $sesi->id_siswa)->where(function ($q) use ($sesi) {
            $q->where('id_surah_mulai', $sesi->id_surah_mulai)->orWhere('id_surah_selesai', $sesi->id_surah_mulai);
        })->where('tanggal_setor', '<=', $sesi->tanggal_setor)->count();

        // PERBAIKAN 4: Rounding di Popup Detail Sesi
        $this->selectedSesiDetail = [
            'id' => $sesi->id_sesi,
            'tanggal' => $sesi->tanggal_setor->format('d F Y'),
            'guru' => $sesi->guru->akun->nama_lengkap ?? '-',
            'ayat_mulai' => $sesi->ayat_mulai,
            'ayat_selesai' => $sesi->ayat_selesai,
            'nilai_tajwid' => round($sesi->skor_tajwid, 2),
            'nilai_kelancaran' => round($sesi->skor_kelancaran, 2),
            'nilai_makhroj' => round($sesi->skor_makhroj, 2),
            'nilai_rata' => round($sesi->nilai_rata, 2),
            'koreksi' => $sesi->koreksi->map(fn($k) => [
                'lokasi' => 'Ayat ' . ($k->ayat->nomor_ayat ?? $k->kata_ke),
                'sesi_ke' => $urutan,
                'jenis_kesalahan' => $k->kategori_kesalahan,
                'catatan' => $k->catatan
            ])
        ];
    }
    public function closeSesiDetail()
    {
        $this->selectedSesiDetail = null;
    }

    // DOWNLOADS
    public function downloadPdf()
    {
        if (!$this->selectedKelompokId)
            return;
        return redirect()->route('export.laporan-hafalan-kelompok.pdf', $this->selectedKelompokId);
    }
    public function downloadExcel()
    {
        if (!$this->selectedKelompokId)
            return;
        return redirect()->route('export.laporan-hafalan-kelompok.excel', $this->selectedKelompokId);
    }
    public function downloadPdfSiswa()
    {
        if (!$this->selectedSiswaId)
            return;
        return redirect()->away(route('export.laporan-hafalan.pdf-siswa', $this->selectedSiswaId));
    }
    public function downloadExcelSiswa()
    {
        if (!$this->selectedSiswaId)
            return;
        return redirect()->away(route('export.laporan-hafalan.excel-siswa', $this->selectedSiswaId));
    }

    // Perbaikan: Parameter untuk download sesi
    public function downloadPdfSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.pdf', ['siswaId' => $this->selectedSiswaId, 'surahId' => $this->selectedSurahId]));
    }
    public function downloadExcelSesi()
    {
        if (!$this->selectedSiswaId || !$this->selectedSurahId)
            return;
        return redirect()->away(route('export.sesi-setoran.excel', ['siswaId' => $this->selectedSiswaId, 'surahId' => $this->selectedSurahId]));
    }

    public function downloadPdfDetailSesi()
    {
        if (!$this->selectedSesiDetail)
            return;
        return redirect()->away(route('export.sesi-detail.pdf', ['sesiId' => $this->selectedSesiDetail['id']]));
    }
    public function downloadExcelDetailSesi()
    {
        if (!$this->selectedSesiDetail)
            return;
        return redirect()->away(route('export.sesi-detail.excel', ['sesiId' => $this->selectedSesiDetail['id']]));
    }

    // NAVIGASI
    public function backToList()
    {
        $this->selectedKelompokId = null;
        $this->selectedSiswaId = null;
        $this->detailLaporan = null;
    }
    public function backToKelompok()
    {
        $this->selectedSiswaId = null;
        $this->selectedSurahId = null;
    }
    public function backToSiswa()
    {
        $this->selectedSurahId = null;
        $this->surahDetail = null;
    }
    public function filterPeriode()
    {
        $this->loadDetailSiswa();
    }

    public function render()
    {
        return view('livewire.guru.laporan-hafalan')->layout('layouts.guru');
    }
}