<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $anakList = [];
    public $selectedAnak = null;
    public $selectedAnakData = null;
    
    public $chartPerkembanganLabels = [];
    public $chartPerkembanganTajwid = [];
    public $chartPerkembanganMakhroj = [];
    public $chartPerkembanganKelancaran = [];
    
    public $chartTargetLabels = [];
    public $chartTargetPencapaian = [];
    public $chartTargetTarget = [];
    
    public $nilaiAspekTajwid = 0;
    public $nilaiAspekMakhroj = 0;
    public $nilaiAspekKelancaran = 0;

    public function mount()
    {
        $this->loadAnakList();
    }

    public function loadAnakList()
    {
        $user = auth()->user();
        Log::info('[v0] Auth User ID: ' . $user->id_akun . ' Name: ' . $user->nama_lengkap);
        
        $orangTua = $user->ortu()->first();
        Log::info('[v0] OrangTua Result:', ['orangTua' => $orangTua ? 'Found' : 'NULL']);
        
        if ($orangTua) {
            Log::info('[v0] OrangTua ID: ' . $orangTua->id_ortu);
            
            $siswaBefore = $orangTua->siswa();
            Log::info('[v0] Siswa Query Before Get:', ['query' => $siswaBefore->toSql(), 'bindings' => $siswaBefore->getBindings()]);
            
            $siswaRaw = $orangTua->siswa()->with('kelas')->get();
            Log::info('[v0] Siswa Count: ' . $siswaRaw->count());
            Log::info('[v0] Siswa Data:', ['data' => $siswaRaw->toArray()]);
            
            $this->anakList = $siswaRaw
                ->map(function ($siswa) {
                    // Hitung progress hafalan
                    $totalSesi = SesiHafalan::where('id_siswa', $siswa->id_siswa)->count();
                    $rataRataNilai = SesiHafalan::where('id_siswa', $siswa->id_siswa)->avg('nilai_rata') ?? 0;
                    
                    $setoranTerakhir = SesiHafalan::where('id_siswa', $siswa->id_siswa)
                        ->with(['surahMulai', 'surahSelesai'])
                        ->orderBy('tanggal_setor', 'DESC')
                        ->limit(2)
                        ->get()
                        ->map(function ($sesi) {
                            return [
                                'surah' => $sesi->surahMulai->nama_surah ?? 'Unknown',
                                'nilai' => number_format($sesi->nilai_rata, 0)
                            ];
                        })
                        ->toArray();
                    
                    return [
                        'id_siswa' => $siswa->id_siswa,
                        'nama_siswa' => $siswa->nama_siswa,
                        'kelas' => $siswa->kelas->nama_kelas,
                        'progress' => 60, // Placeholder, bisa disesuaikan dengan logic
                        'total_sesi' => $totalSesi,
                        'rata_rata' => number_format($rataRataNilai, 0),
                        'setoran_terakhir' => $setoranTerakhir,
                    ];
                })
                ->toArray();
                
            Log::info('[v0] AnakList:', ['count' => count($this->anakList), 'data' => $this->anakList]);
        } else {
            Log::info('[v0] OrangTua is null for user:', ['userId' => $user->id_akun]);
        }
    }

    public function selectAnak($idSiswa)
    {
        $this->selectedAnak = $idSiswa;
        $this->loadDetailAnak($idSiswa);
    }

    public function loadDetailAnak($idSiswa)
    {
        $siswa = Siswa::find($idSiswa);
        $this->selectedAnakData = [
            'id_siswa' => $siswa->id_siswa,
            'nama_siswa' => $siswa->nama_siswa,
            'kelas' => $siswa->kelas->nama_kelas,
        ];

        // 1. Chart Perkembangan Nilai (4 minggu terakhir)
        $sevenDaysAgo = now()->subDays(28);
        $sesiTerakhir = SesiHafalan::where('id_siswa', $idSiswa)
            ->where('tanggal_setor', '>=', $sevenDaysAgo)
            ->orderBy('tanggal_setor')
            ->get();

        $minggu = [];
        $tajwidData = [];
        $makrojData = [];
        $kelancaran = [];

        foreach ($sesiTerakhir->groupBy(function ($item) {
            return $item->tanggal_setor->floorWeek(Carbon::MONDAY)->format('Y-m-d');
        }) as $week => $sessions) {
            $minggu[] = 'Minggu ' . count($minggu) + 1;
            $tajwidData[] = $sessions->avg('skor_tajwid');
            $makrojData[] = $sessions->avg('skor_makhroj');
            $kelancaran[] = $sessions->avg('skor_kelancaran');
        }

        $this->chartPerkembanganLabels = $minggu;
        $this->chartPerkembanganTajwid = $tajwidData;
        $this->chartPerkembanganMakhroj = $makrojData;
        $this->chartPerkembanganKelancaran = $kelancaran;

        // 2. Chart Target Hafalan Bulanan
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        
        $targetBulan = TargetHafalanKelompok::where('id_kelompok', $siswa->id_kelas)
            ->whereMonth('tanggal_mulai', $bulanIni)
            ->whereYear('tanggal_mulai', $tahunIni)
            ->first();

        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $pencapaianData = [15, 18, 22, 20, 21, 19]; // Placeholder
        $targetData = [20, 20, 20, 20, 20, 20]; // Placeholder

        $this->chartTargetLabels = $bulanLabels;
        $this->chartTargetPencapaian = $pencapaianData;
        $this->chartTargetTarget = $targetData;

        // 3. Nilai Per Aspek Penilaian
        $avgTajwid = SesiHafalan::where('id_siswa', $idSiswa)->avg('skor_tajwid') ?? 0;
        $avgMakhroj = SesiHafalan::where('id_siswa', $idSiswa)->avg('skor_makhroj') ?? 0;
        $avgKelancaran = SesiHafalan::where('id_siswa', $idSiswa)->avg('skor_kelancaran') ?? 0;

        $this->nilaiAspekTajwid = round($avgTajwid);
        $this->nilaiAspekMakhroj = round($avgMakhroj);
        $this->nilaiAspekKelancaran = round($avgKelancaran);
    }

    public function render()
    {
        return view('livewire.orang-tua.dashboard')->layout('layouts.orang-tua');
    }
}
