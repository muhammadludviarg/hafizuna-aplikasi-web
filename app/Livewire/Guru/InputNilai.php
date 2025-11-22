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
use App\Jobs\SendNotifikasiOrtuJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

    // Data Sesi yang Dipilih
    public $guru;
    public $selectedKelompokId;
    public $selectedSiswaId;
    public $selectedSiswaNama;

    // Data Form Surah (Step 3)
    public $id_surah, $ayat_mulai, $ayat_selesai;

    // (BARU) Properti untuk menampilkan jumlah ayat
    public $jumlahAyatSurah = null;

    // Data untuk Penilaian (Step 4)
    public $ayatsToReview = [];
    public $koreksi = [];

    // Aturan validasi baru
    protected $rules = [
        'id_surah' => 'required|integer|exists:surah,id_surah',
        'ayat_mulai' => 'required|integer|min:1',
        'ayat_selesai' => 'required|integer|min:1|gte:ayat_mulai',
    ];

    /**
     * Mount: (Dijalankan sekali saat load)
     */
    public function mount()
    {
        // Mode Development: Ambil guru pertama jika tidak login
        if (!Auth::check()) {
            $this->guru = Guru::first();
            if (!$this->guru) {
                session()->flash('error', 'MODE DEVELOPMENT: Tabel guru kosong, tidak bisa mengambil data tes.');
                return;
            }
        } else {
            $this->guru = Auth::user()->guru;
            if (!$this->guru) {
                session()->flash('error', 'Akun Anda tidak terdaftar sebagai guru.');
                return redirect()->route('dashboard');
            }
        }

        $this->daftarKelompok = Kelompok::where('id_guru', $this->guru->id_guru)
            ->with('kelas')
            ->get();
        $this->daftarSurah = Surah::orderBy('nomor_surah')->get();
    }

    // (BARU) Lifecycle Hook. Dijalankan SETIAP KALI $id_surah berubah.
    public function updatedIdSurah($value)
    {
        if ($value) {
            $surah = Surah::find($value);
            $this->jumlahAyatSurah = $surah->jumlah_ayat;
            // Validasi otomatis ayat_selesai jika melebihi
            if ($this->ayat_selesai > $this->jumlahAyatSurah) {
                $this->ayat_selesai = $this->jumlahAyatSurah;
            }
        } else {
            $this->jumlahAyatSurah = null;
        }
    }


    /** STEP 1: Pilih Kelompok **/
    public function selectKelompok($kelompokId)
    {
        $this->selectedKelompokId = $kelompokId;
        $this->daftarSiswa = Siswa::whereHas('kelompok', function ($query) use ($kelompokId) {
            $query->where('siswa_kelompok.id_kelompok', $kelompokId);
        })->with('kelas')->get();

        $this->step = 2;
    }

    /** STEP 2: Pilih Siswa **/
    public function selectSiswa($siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSiswaNama = Siswa::find($siswaId)->nama_siswa;
        $this->step = 3;
    }

    /** STEP 3: Load Ayat **/
    public function loadAyats()
    {
        $this->validate();

        $this->ayatsToReview = Ayat::where('id_surah', $this->id_surah)
            ->whereBetween('nomor_ayat', [$this->ayat_mulai, $this->ayat_selesai])
            ->orderBy('nomor_ayat')
            ->get();

        if ($this->ayatsToReview->isEmpty()) {
            session()->flash('error', 'Ayat tidak ditemukan. Periksa kembali rentang yang Anda masukkan.');
            return;
        }

        $this->koreksi = [];
        $this->step = 4;
    }

    /** STEP 4: Tambah Koreksi **/
    public function addKoreksi($idAyat, $kataKe, $kategori, $kataArab)
    {
        $key = 'id_ayat_' . $idAyat . '_kata_' . $kataKe;

        if (isset($this->koreksi[$key]) && $this->koreksi[$key]['kategori'] == $kategori) {
            unset($this->koreksi[$key]);
        } else {
            $this->koreksi[$key] = [
                'id_ayat' => $idAyat,
                'kata_ke' => $kataKe,
                'kategori' => $kategori,
                'kata_arab' => $kataArab,
            ];
        }
    }

    /** Computed Property untuk Statistik Real-time **/
    #[Computed]
    public function statistik()
    {
        $totalKata = 0;
        foreach ($this->ayatsToReview as $ayat) {
            $totalKata += $ayat->jumlah_kata;
        }
        if ($totalKata == 0)
            $totalKata = 1;

        $koreksiCol = collect($this->koreksi);
        $totalKesalahanTajwid = $koreksiCol->where('kategori', 'tajwid')->count();
        $totalKesalahanMakhroj = $koreksiCol->where('kategori', 'makhroj')->count();
        $totalKesalahanKelancaran = $koreksiCol->where('kategori', 'kelancaran')->count();

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

    /** * STEP 4: Simpan Sesi
     * LOGIKA INI SUDAH BENAR!
     */
    public function simpanSesi($kirimNotifikasi = false)
    {
        $statistik = $this->statistik();
        $sesi = null;

        // 1. Jalankan Transaksi Database
        // Ini akan menyimpan SesiHafalan dan Koreksi.
        // Jika salah satu gagal, keduanya akan di-rollback. Ini SUDAH BENAR.
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

            foreach ($this->koreksi as $k) {
                Koreksi::create([
                    'id_sesi' => $sesi->id_sesi,
                    'id_ayat' => $k['id_ayat'],
                    'kata_ke' => $k['kata_ke'] + 1,
                    'kategori_kesalahan' => $k['kategori'],
                    'catatan' => "Kesalahan kata: " . $k['kata_arab'],
                ]);
            }
        });

        // 2. Coba Kirim Notifikasi (HANYA JIKA 'kirimNotifikasi' true DAN $sesi berhasil dibuat)
        if ($kirimNotifikasi && $sesi) {
            try {
                // 3. Dispatch ke Antrean (Queue)
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

    /** Navigasi & Reset **/
    public function backStep($step)
    {
        $this->step = $step;
    }
    public function resetAll()
    {
        $this->reset('step', 'selectedKelompokId', 'selectedSiswaId', 'selectedSiswaNama', 'id_surah', 'ayat_mulai', 'ayat_selesai', 'ayatsToReview', 'koreksi', 'jumlahAyatSurah');
        $this->mount();
    }
    public function render()
    {
        return view('livewire.guru.input-nilai')
        ->layout('layouts.guru');
    }
}