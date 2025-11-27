<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $anakList = [];
    public $selectedAnakId = null;
    public $selectedAnakData = null;

    // Variabel Statistik Single
    public $nilaiAspekTajwid = 0;
    public $nilaiAspekKelancaran = 0;
    public $nilaiAspekMakhroj = 0;

    // Variabel Chart Perkembangan (Line Chart)
    public $chartPerkembanganLabels = [];
    public $chartPerkembanganTajwid = [];
    public $chartPerkembanganMakhroj = [];
    public $chartPerkembanganKelancaran = [];

    // Variabel Chart Target (Bar Chart)
    public $chartTargetLabels = [];
    public $chartTargetPencapaian = [];
    public $chartTargetTarget = [];

    public function mount()
    {
        $this->loadAnak();
    }

    public function loadAnak()
    {
        $user = Auth::user();
        if (!$user->ortu)
            return;

        // Load anak beserta data ringkas untuk card list
        $this->anakList = Siswa::where('id_ortu', $user->ortu->id_ortu)
            ->get()
            ->map(function ($siswa) {
                $sesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)->get();
                $totalAyat = 0;
                foreach ($sesi as $s) {
                    $totalAyat += ($s->ayat_selesai - $s->ayat_mulai + 1);
                }

                // Ambil setoran terakhir
                $lastSesi = $sesi->sortByDesc('tanggal_setor')->take(2);
                $setoranTerakhir = $lastSesi->map(function ($s) {
                    return [
                        'surah' => $s->surahMulai->nama_surah ?? '-',
                        'nilai' => $s->nilai_rata
                    ];
                });

                return [
                    'id_siswa' => $siswa->id_siswa,
                    'nama_siswa' => $siswa->nama_siswa,
                    'kelas' => $siswa->kelas->nama_kelas ?? '-',
                    'total_sesi' => $sesi->count(),
                    'total_ayat' => $totalAyat,
                    'rata_rata' => $sesi->count() > 0 ? round($sesi->avg('nilai_rata'), 1) : 0,
                    'progress' => min(100, round(($totalAyat / 1000) * 100)), // Asumsi target 1000 ayat
                    'setoran_terakhir' => $setoranTerakhir
                ];
            })->toArray();

        // Auto select anak pertama
        if (!empty($this->anakList)) {
            $this->selectAnak($this->anakList[0]['id_siswa']);
        }
    }

    public function selectAnak($siswaId)
    {
        $this->selectedAnakId = $siswaId;
        $this->selectedAnakData = collect($this->anakList)->firstWhere('id_siswa', $siswaId);
        $this->loadStatistikDetail();

        // Kirim event ke JS untuk update chart
        $this->dispatch('update-charts', [
            'perkembangan' => [
                'labels' => $this->chartPerkembanganLabels,
                'tajwid' => $this->chartPerkembanganTajwid,
                'makhroj' => $this->chartPerkembanganMakhroj,
                'kelancaran' => $this->chartPerkembanganKelancaran
            ],
            'target' => [
                'labels' => $this->chartTargetLabels,
                'pencapaian' => $this->chartTargetPencapaian,
                'target' => $this->chartTargetTarget
            ]
        ]);
    }

    public function loadStatistikDetail()
    {
        if (!$this->selectedAnakId)
            return;

        $sesiHafalan = SesiHafalan::where('id_siswa', $this->selectedAnakId)
            ->orderBy('tanggal_setor', 'asc')
            ->get();

        if ($sesiHafalan->isEmpty()) {
            $this->resetCharts();
            return;
        }

        // 1. Hitung Rata-rata Aspek (Untuk Progress Bar)
        $this->nilaiAspekTajwid = round($sesiHafalan->avg('skor_tajwid'), 1);
        $this->nilaiAspekKelancaran = round($sesiHafalan->avg('skor_kelancaran'), 1);
        $this->nilaiAspekMakhroj = round($sesiHafalan->avg('skor_makhroj'), 1);

        // 2. Siapkan Data Chart Perkembangan (10 Sesi Terakhir)
        $dataGrafik = $sesiHafalan->take(-10);

        $this->chartPerkembanganLabels = $dataGrafik->map(fn($s) => $s->tanggal_setor->format('d M'))->toArray();
        $this->chartPerkembanganTajwid = $dataGrafik->pluck('skor_tajwid')->toArray();
        $this->chartPerkembanganMakhroj = $dataGrafik->pluck('skor_makhroj')->toArray();
        $this->chartPerkembanganKelancaran = $dataGrafik->pluck('skor_kelancaran')->toArray();

        // 3. Siapkan Data Chart Target Bulanan (3 Bulan Terakhir)
        // Kelompokkan data per bulan
        $groupedByMonth = $sesiHafalan->groupBy(function ($date) {
            return Carbon::parse($date->tanggal_setor)->format('M Y'); // Jan 2024
        })->take(-4); // Ambil 4 bulan terakhir

        $this->chartTargetLabels = [];
        $this->chartTargetPencapaian = [];
        $this->chartTargetTarget = [];

        foreach ($groupedByMonth as $month => $sessions) {
            $this->chartTargetLabels[] = $month;

            // Hitung total ayat bulan ini
            $ayatBulanIni = 0;
            foreach ($sessions as $s) {
                $ayatBulanIni += ($s->ayat_selesai - $s->ayat_mulai + 1);
            }
            $this->chartTargetPencapaian[] = $ayatBulanIni;
            $this->chartTargetTarget[] = 50; // Target statis 50 ayat per bulan (bisa disesuaikan)
        }
    }

    private function resetCharts()
    {
        $this->nilaiAspekTajwid = 0;
        $this->nilaiAspekKelancaran = 0;
        $this->nilaiAspekMakhroj = 0;
        $this->chartPerkembanganLabels = [];
        $this->chartPerkembanganTajwid = [];
        $this->chartTargetLabels = [];
        $this->chartTargetPencapaian = [];
    }

    public function render()
    {
        return view('livewire.orang-tua.dashboard')
            ->layout('layouts.orang-tua');
    }
}