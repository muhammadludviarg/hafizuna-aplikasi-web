<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanHafalan extends Component
{
    public $anakList = [];

    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null;

    public $siswaDetail = null;
    public $surahDetail = null;

    public $tanggalMulai = null;
    public $tanggalAkhir = null;

    // --- PROPERTY PAGINATION ---
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
        $this->loadAnakList();
    }

    public function loadAnakList()
    {
        $user = Auth::user();
        $ortu = $user->ortu;

        if (!$ortu) {
            $this->anakList = [];
            return;
        }

        $this->anakList = Siswa::where('id_ortu', $ortu->id_ortu)
            ->with('kelompok')
            ->get()
            ->map(function ($siswa) {
                $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
                $jumlahSesi = $sesiHafalan->count();
                $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

                // Hitung Progress Target (Preview di Card Depan)
                $kelompok = $siswa->kelompok->first();
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
                    'jumlah_sesi' => $jumlahSesi,
                    'nilai_rata_rata' => $nilaiRataRata,
                    'progress_target' => $progressTarget,
                ];
            })
            ->toArray();

        // Jika hanya punya 1 anak, langsung pilih otomatis
        if (count($this->anakList) === 1) {
            $this->selectSiswa($this->anakList[0]['id_siswa']);
        }
    }

    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSurahId = null;
        $this->tanggalMulai = null;
        $this->tanggalAkhir = null;

        // Reset Pagination
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

        // --- 1. RIWAYAT SESI (Dengan Filter Tanggal & Pagination) ---
        $query = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->with('surahMulai', 'surahSelesai');

        if ($this->tanggalMulai)
            $query->whereDate('tanggal_setor', '>=', $this->tanggalMulai);
        if ($this->tanggalAkhir)
            $query->whereDate('tanggal_setor', '<=', $this->tanggalAkhir);

        $allRiwayatSesi = $query->orderByDesc('tanggal_setor')->get();

        // Statistik
        $jumlahSesi = $allRiwayatSesi->count();
        $nilaiTajwid = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_tajwid'), 2) : 0;
        $nilaiKelancaran = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_kelancaran'), 2) : 0;
        $nilaiMakhroj = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('skor_makhroj'), 2) : 0;
        $nilaiRataRata = $jumlahSesi > 0 ? round($allRiwayatSesi->avg('nilai_rata'), 2) : 0;

        // Pagination Manual Sesi
        $this->totalPagesSesi = ceil($jumlahSesi / $this->perPageSesi);
        if ($this->currentPageSesi > $this->totalPagesSesi && $this->totalPagesSesi > 0)
            $this->currentPageSesi = 1;
        $offsetSesi = ($this->currentPageSesi - 1) * $this->perPageSesi;
        $riwayatSesiPaged = $allRiwayatSesi->slice($offsetSesi, $this->perPageSesi);

        $riwayatFormatted = $riwayatSesiPaged->map(function ($sesi) {
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
        })->values()->toArray();

        // --- 2. SURAH SUDAH DIHAFAL (TUNTAS) ---
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

        // Pagination Surah Tuntas
        $totalSurahSelesai = count($allSurahSelesai);
        $this->totalPagesSurahSelesai = ceil($totalSurahSelesai / $this->perPageSurahSelesai);
        $offsetSurah = ($this->currentPageSurahSelesai - 1) * $this->perPageSurahSelesai;
        $surahSudahDihafalPaged = array_slice($allSurahSelesai, $offsetSurah, $this->perPageSurahSelesai);

        // --- 3. TARGET HAFALAN ---
        $siswaKelompok = $siswa->kelompok;
        $allTargetBelum = [];

        if ($siswaKelompok->isNotEmpty()) {
            $kelompokIds = $siswaKelompok->pluck('id_kelompok')->toArray();
            $targetHafalan = TargetHafalanKelompok::whereIn('id_kelompok', $kelompokIds)->get();

            foreach ($targetHafalan as $target) {
                $rangeSurah = range(min($target->id_surah_awal, $target->id_surah_akhir), max($target->id_surah_awal, $target->id_surah_akhir));

                foreach ($rangeSurah as $i) {
                    // Skip jika sudah tuntas
                    $isTuntas = isset($statsSurah[$i]) && $statsSurah[$i]['max_ayat'] >= $statsSurah[$i]['jumlah_ayat_surah'];

                    if (!$isTuntas) {
                        $surah = Surah::find($i);
                        if ($surah) {
                            $cekSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                                ->where(function ($q) use ($i) {
                                    $q->where('id_surah_mulai', $i)->orWhere('id_surah_selesai', $i);
                                })->orderByDesc('ayat_selesai')->first();

                            $status = $cekSesi ? 'Sedang Menghafal' : 'Belum Dimulai';
                            $progress = $cekSesi ? $cekSesi->ayat_selesai . '/' . $surah->jumlah_ayat . ' ayat' : '0/' . $surah->jumlah_ayat . ' ayat';

                            $allTargetBelum[] = [
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

        // Pagination Target
        $totalTarget = count($allTargetBelum);
        $this->totalPagesTarget = ceil($totalTarget / $this->perPageTarget);
        $offsetTarget = ($this->currentPageTarget - 1) * $this->perPageTarget;
        $targetBelumDihafalkanPaged = array_slice($allTargetBelum, $offsetTarget, $this->perPageTarget);

        $this->siswaDetail = [
            'nama_siswa' => $siswa->nama_siswa,
            'jumlah_sesi' => $jumlahSesi,
            'nilai_tajwid' => $nilaiTajwid,
            'nilai_kelancaran' => $nilaiKelancaran,
            'nilai_makhroj' => $nilaiMakhroj,
            'nilai_rata_rata' => $nilaiRataRata,
            'riwayat_sesi' => $riwayatFormatted,
            'target_belum_dihafalkan' => $targetBelumDihafalkanPaged,
            'surah_sudah_dihafal' => $surahSudahDihafalPaged,
        ];
    }

    // --- NAVIGATION METHODS FOR PAGINATION ---
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

    // --- DETAIL SESI & SURAH ---

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

        $sesiSurah = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
            ->where(function ($q) use ($surah) {
                $q->where('id_surah_mulai', $surah->id_surah)
                    ->orWhere('id_surah_selesai', $surah->id_surah);
            })
            ->with('surahMulai', 'surahSelesai')
            ->orderByDesc('tanggal_setor')
            ->get();

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

    public function backToSiswaList()
    {
        $this->selectedSiswaId = null;
        $this->siswaDetail = null;
        $this->selectedSurahId = null;
        $this->detailLaporan = null;
    }

    public function backToSiswa()
    {
        $this->selectedSurahId = null;
        $this->surahDetail = null;
        if ($this->selectedSiswaId)
            $this->loadDetailSiswa();
    }

    // --- DOWNLOAD ---
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
        return view('livewire.orang-tua.laporan-hafalan')->layout('layouts.orang-tua');
    }
}