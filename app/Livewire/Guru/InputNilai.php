<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\SiswaKelompok;
use App\Models\Surah;
use App\Models\Ayat;
use App\Models\SesiHafalan;
use App\Models\Koreksi;
use App\Models\Guru;
use App\Models\TargetHafalanKelompok;
use App\Jobs\SendNotifikasiOrtuJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;

class InputNilai extends Component
{
    // State Halaman
    public $step = 1;

    // Data untuk Pilihan
    public $daftarKelompok = [];
    public $daftarSiswa = [];
    public $daftarSurah = [];

    public $searchSiswa = '';

    // Data Sesi yang Dipilih
    public $guru;
    public $selectedKelompokId;
    public $selectedSiswaId;
    public $selectedSiswaNama;

    // (BARU) Info Target Hafalan untuk ditampilkan di UI
    public $targetHafalanInfo = null;
    public $lastHafalan = null;

    // Data Form Surah (Step 3)
    public $id_surah, $ayat_mulai, $ayat_selesai;

    // Properti untuk menampilkan jumlah ayat
    public $jumlahAyatSurah = null;

    // Data untuk Penilaian (Step 4)
    public $ayatsToReview = [];
    public $koreksi = [];

    // Aturan validasi
    protected $rules = [
        'id_surah' => 'required|integer|exists:surah,id_surah',
        'ayat_mulai' => 'required|integer|min:1',
        'ayat_selesai' => 'required|integer|min:1|gte:ayat_mulai',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            $this->guru = Guru::first();
            if (!$this->guru) {
                session()->flash('error', 'MODE DEVELOPMENT: Tabel guru kosong.');
                return;
            }
        } else {
            $this->guru = Auth::user()->guru;
            if (!$this->guru) {
                session()->flash('error', 'Akun Anda tidak terdaftar sebagai guru.');
                return redirect()->route('dashboard');
            }
        }

        $this->daftarKelompok = Kelompok::with(['kelas', 'siswa'])
            ->where('id_guru', $this->guru->id_guru)
            ->get()
            ->map(function ($kelompok) {
                $jumlahSiswa = $kelompok->siswa->count();
                $namaKelas = $kelompok->kelas ? $kelompok->kelas->nama_kelas : 'Tanpa Kelas';
                $tahunAjaran = $kelompok->kelas ? $kelompok->kelas->tahun_ajaran : '-';

                return [
                    'id' => $kelompok->id_kelompok,
                    'nama_kelompok_utama' => $kelompok->nama_kelompok ?? 'Kelompok',
                    'nama_kelas_kecil' => $namaKelas,
                    'tahun_ajaran' => $tahunAjaran,
                    'jumlah_siswa' => $jumlahSiswa,
                ];
            })
            ->toArray();

        // Load awal semua surah (tanpa status)
        $this->daftarSurah = Surah::orderBy('nomor_surah')->get()->toArray();
    }

    public function updatedIdSurah($value)
    {
        if ($value) {
            // Find surah from array
            $surah = collect($this->daftarSurah)->firstWhere('id_surah', $value);
            if ($surah) {
                $this->jumlahAyatSurah = $surah['jumlah_ayat'];
                if ($this->ayat_selesai > $this->jumlahAyatSurah) {
                    $this->ayat_selesai = $this->jumlahAyatSurah;
                }
            }
        } else {
            $this->jumlahAyatSurah = null;
        }
    }

    public function selectKelompok($kelompokId)
    {
        $this->selectedKelompokId = $kelompokId;
        $this->searchSiswa = '';
        $this->daftarSiswa = Siswa::whereHas('kelompok', function ($query) use ($kelompokId) {
            $query->where('siswa_kelompok.id_kelompok', $kelompokId);
        })->with('kelas')->get();

        $this->step = 2;
    }

    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSiswaNama = Siswa::find($siswaId)->nama_siswa;

        // Load status hafalan & Info Target
        $this->loadStatusHafalanSiswa();

        $this->step = 3;
    }

    public function loadStatusHafalanSiswa()
    {
        // 1. Ambil Target Hafalan
        $target = TargetHafalanKelompok::where('id_kelompok', $this->selectedKelompokId)->first();

        if ($target && $target->surahAwal && $target->surahAkhir) {
            $this->targetHafalanInfo = "Target: " . $target->surahAwal->nama_surah . " s.d " . $target->surahAkhir->nama_surah;
        } else {
            $this->targetHafalanInfo = "Belum ada target hafalan yang diatur.";
        }

        // 2. (PERBAIKAN) Ambil Hafalan Terakhir dengan Relasi yang BENAR
        // Ganti 'with('surah')' menjadi 'with('surahMulai')' karena itu nama fungsi di Model
        $this->lastHafalan = SesiHafalan::with('surahMulai')
            ->where('id_siswa', $this->selectedSiswaId)
            ->orderBy('tanggal_setor', 'desc') // Pastikan pakai tanggal_setor
            ->first();

        // 3. Load Surah + Status
        $this->daftarSurah = Surah::orderBy('nomor_surah')->get()->map(function ($surah) {
            $sesi = SesiHafalan::where('id_siswa', $this->selectedSiswaId)
                ->where(function ($q) use ($surah) {
                    $q->where('id_surah_mulai', $surah->id_surah)
                        ->orWhere('id_surah_selesai', $surah->id_surah);
                })
                ->latest('tanggal_setor')
                ->first();

            $statusHafalan = 'Belum';
            $statusColor = 'text-gray-500';

            if ($sesi) {
                if ($sesi->ayat_selesai >= $surah->jumlah_ayat) {
                    $statusHafalan = 'Selesai';
                    $statusColor = 'text-green-600 font-bold';
                } else {
                    $statusHafalan = 'Sedang';
                    $statusColor = 'text-yellow-600 font-bold';
                }
            }

            return [
                'id_surah' => $surah->id_surah,
                'nomor_surah' => $surah->nomor_surah,
                'nama_surah' => $surah->nama_surah,
                'jumlah_ayat' => $surah->jumlah_ayat,
                'status_hafalan' => $statusHafalan,
                'status_color' => $statusColor,
            ];
        })->toArray();
    }

    public function loadAyats()
    {
        $this->validate();

        $this->ayatsToReview = Ayat::where('id_surah', $this->id_surah)
            ->whereBetween('nomor_ayat', [$this->ayat_mulai, $this->ayat_selesai])
            ->orderBy('nomor_ayat')
            ->get()
            ->toArray();

        if (empty($this->ayatsToReview)) {
            session()->flash('error', 'Ayat tidak ditemukan. Periksa kembali rentang yang Anda masukkan.');
            return;
        }

        $this->koreksi = [];
        $this->step = 4;
    }

    /**
     * FUNGSI BARU: Toggle kesalahan (support multiple kategori per kata)
     */
    public function toggleKoreksi($idAyat, $kataKe, $kategori, $kataArab)
    {
        $key = 'id_ayat_' . $idAyat . '_kata_' . $kataKe;

        // Inisialisasi array jika belum ada
        if (!isset($this->koreksi[$key])) {
            $this->koreksi[$key] = [
                'id_ayat' => $idAyat,
                'kata_ke' => $kataKe,
                'kata_arab' => $kataArab,
                'kategori' => [], // Array untuk multiple kategori
            ];
        }

        // Toggle kategori (tambah jika belum ada, hapus jika sudah ada)
        if (in_array($kategori, $this->koreksi[$key]['kategori'])) {
            // Hapus kategori
            $this->koreksi[$key]['kategori'] = array_values(
                array_filter($this->koreksi[$key]['kategori'], fn($k) => $k !== $kategori)
            );

            // Hapus entry jika tidak ada kategori lagi
            if (empty($this->koreksi[$key]['kategori'])) {
                unset($this->koreksi[$key]);
            }
        } else {
            // Tambah kategori
            $this->koreksi[$key]['kategori'][] = $kategori;
        }
    }

    /**
     * FUNGSI BARU: Helper untuk mengecek apakah kategori tertentu sudah dipilih
     */
    public function isKoreksiChecked($idAyat, $kataKe, $kategori)
    {
        $key = 'id_ayat_' . $idAyat . '_kata_' . $kataKe;
        return isset($this->koreksi[$key]) && in_array($kategori, $this->koreksi[$key]['kategori']);
    }

    #[Computed]
    public function statistik()
    {
        $totalKata = 0;
        foreach ($this->ayatsToReview as $ayat) {
            $totalKata += $ayat['jumlah_kata'];
        }
        if ($totalKata == 0)
            $totalKata = 1;

        // REVISI: Hitung kesalahan dari struktur data baru
        $totalKesalahanTajwid = 0;
        $totalKesalahanMakhroj = 0;
        $totalKesalahanKelancaran = 0;

        foreach ($this->koreksi as $k) {
            if (in_array('tajwid', $k['kategori'])) {
                $totalKesalahanTajwid++;
            }
            if (in_array('makhroj', $k['kategori'])) {
                $totalKesalahanMakhroj++;
            }
            if (in_array('kelancaran', $k['kategori'])) {
                $totalKesalahanKelancaran++;
            }
        }

        $proporsiTajwid = ($totalKesalahanTajwid / $totalKata) * 100;
        $proporsiMakhroj = ($totalKesalahanMakhroj / $totalKata) * 100;
        $proporsiKelancaran = ($totalKesalahanKelancaran / $totalKata) * 100;

        $skorTajwid = 100 - $proporsiTajwid;
        $skorMakhroj = 100 - $proporsiMakhroj;
        $skorKelancaran = 100 - $proporsiKelancaran;

        $nilaiAkhir = ($skorTajwid + $skorMakhroj + $skorKelancaran) / 3;

        return [
            'totalKata' => $totalKata,
            'totalKesalahanTajwid' => $totalKesalahanTajwid,
            'totalKesalahanMakhroj' => $totalKesalahanMakhroj,
            'totalKesalahanKelancaran' => $totalKesalahanKelancaran,
            'proporsiTajwid' => round($proporsiTajwid, 2),
            'proporsiMakhroj' => round($proporsiMakhroj, 2),
            'proporsiKelancaran' => round($proporsiKelancaran, 2),
            'skorTajwid' => round($skorTajwid, 2),
            'skorMakhroj' => round($skorMakhroj, 2),
            'skorKelancaran' => round($skorKelancaran, 2),
            'nilaiAkhir' => round($nilaiAkhir, 2),
        ];
    }

    #[Computed]
    public function filteredSiswa()
    {
        if (empty($this->searchSiswa)) {
            return $this->daftarSiswa;
        }

        return $this->daftarSiswa->filter(function ($siswa) {
            return stripos($siswa->nama_siswa, $this->searchSiswa) !== false;
        });
    }

    public function simpanSesi($kirimNotifikasi = false)
    {
        $statistik = $this->statistik();
        $sesi = null;

        DB::transaction(function () use (&$sesi, $statistik) {
            $sesi = SesiHafalan::create([
                'id_siswa' => $this->selectedSiswaId,
                'id_guru' => $this->guru->id_guru,
                'id_surah_mulai' => $this->id_surah,
                'ayat_mulai' => $this->ayat_mulai,
                'id_surah_selesai' => $this->id_surah,
                'ayat_selesai' => $this->ayat_selesai,
                'tanggal_setor' => now(),

                'proporsi_tajwid' => $statistik['proporsiTajwid'],
                'proporsi_makhroj' => $statistik['proporsiMakhroj'],
                'proporsi_kelancaran' => $statistik['proporsiKelancaran'],
                'skor_tajwid' => $statistik['skorTajwid'],
                'skor_makhroj' => $statistik['skorMakhroj'],
                'skor_kelancaran' => $statistik['skorKelancaran'],

                'grade_tajwid' => $statistik['skorTajwid'] > 85 ? 'A' : 'B',
                'grade_makhroj' => $statistik['skorMakhroj'] > 85 ? 'A' : 'B',
                'grade_kelancaran' => $statistik['skorKelancaran'] > 85 ? 'A' : 'B',

                'nilai_rata' => $statistik['nilaiAkhir'],
            ]);

            // REVISI: Simpan setiap kategori kesalahan sebagai record terpisah
            foreach ($this->koreksi as $k) {
                foreach ($k['kategori'] as $kategori) {
                    Koreksi::create([
                        'id_sesi' => $sesi->id_sesi,
                        'id_ayat' => $k['id_ayat'],
                        'kata_ke' => $k['kata_ke'] + 1,
                        'kategori_kesalahan' => $kategori,
                        'catatan' => "Kesalahan {$kategori} pada kata: " . $k['kata_arab'],
                    ]);
                }
            }
        });

        if ($kirimNotifikasi && $sesi) {
            try {
                SendNotifikasiOrtuJob::dispatch($sesi);
                session()->flash('message', 'Sesi berhasil disimpan. Nilai Akhir: ' . $statistik['nilaiAkhir'] . '. Notifikasi sedang dikirim.');
            } catch (\Exception $e) {
                Log::error('Gagal dispatch SendNotifikasiOrtuJob: ' . $e->getMessage());
                session()->flash('error', 'Sesi disimpan, TAPI gagal mengirim notifikasi. Cek log.');
            }
        } else {
            session()->flash('message', 'Sesi hafalan berhasil disimpan. Nilai Akhir: ' . $statistik['nilaiAkhir']);
        }

        $this->resetAll();
    }

    public function backStep($step)
    {
        $this->step = $step;
    }

    public function resetAll()
    {
        $this->reset('step', 'selectedKelompokId', 'selectedSiswaId', 'selectedSiswaNama', 'id_surah', 'ayat_mulai', 'ayat_selesai', 'ayatsToReview', 'koreksi', 'jumlahAyatSurah', 'targetHafalanInfo');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.guru.input-nilai')
            ->layout('layouts.guru');
    }
}