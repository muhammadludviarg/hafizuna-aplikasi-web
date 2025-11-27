<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\Auth;

class LaporanHafalan extends Component
{
    // Tidak ada Kelas List di sini
    public $anakList = [];

    // Langsung mulai dari Siswa
    public $selectedSiswaId = null;
    public $selectedSurahId = null;
    public $selectedSesiDetail = null;

    public $siswaDetail = null;
    public $surahDetail = null;

    public $tanggalMulai = null;
    public $tanggalAkhir = null;

    public function mount()
    {
        $this->loadAnakList();
    }

    public function loadAnakList()
    {
        $user = Auth::user();
        $ortu = $user->ortu; // Relasi User ke OrangTua

        if (!$ortu) {
            $this->anakList = [];
            return;
        }

        // AMBIL ANAK DARI ORANG TUA INI
        // Langsung load data detailnya biar mirip struktur Admin tapi versi mini
        $this->anakList = Siswa::where('id_ortu', $ortu->id_ortu)
            ->get()
            ->map(function ($siswa) {
                $sesiHafalan = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
                $jumlahSesi = $sesiHafalan->count();
                $nilaiRataRata = $jumlahSesi > 0 ? round($sesiHafalan->avg('nilai_rata'), 2) : 0;

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
            })
            ->toArray();

        // Jika anak cuma 1, otomatis pilih
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
    public function backToSiswaList()
    {
        $this->selectedSiswaId = null;
        $this->siswaDetail = null;
        $this->selectedSurahId = null;
    }

    public function backToSiswa() // Kembali dari Surah ke Detail Siswa
    {
        $this->selectedSurahId = null;
        $this->surahDetail = null;
        if ($this->selectedSiswaId)
            $this->loadDetailSiswa();
    }

    // --- DOWNLOAD ---
    // Sekarang menggunakan route umum (tanpa prefix 'admin.')

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