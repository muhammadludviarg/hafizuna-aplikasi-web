<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Guru;
use App\Models\SesiHafalan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $namaGuru;
    public $kelasCount;
    public $siswaCount;
    public $rataRataNilai;
    public $sesiBlnIni;
    
    public $chartProgressLabels = [];
    public $chartProgressData = [];
    
    public $chartTrenLabels = [];
    public $chartTrenTajwid = [];
    public $chartTrenMakhroj = [];
    public $chartTrenKelancaran = [];
    
    public $chartDistribusiLabels = [];
    public $chartDistribusiData = [];
    
    public $setorTerbaru = [];

    public function mount()
    {
        $user = auth()->user();
        $guru = $user->guru; // Using relation defined in User model
        
        if (!$guru) {
            abort(403, 'Guru not found');
        }
        
        $this->namaGuru = $user->nama_lengkap ?? 'Ustadz';
        
        // 1. Kelas Diampu (COUNT DISTINCT dari Kelompok)
        $this->kelasCount = DB::table('kelompok')
            ->where('id_guru', $guru->id_guru)
            ->distinct('id_kelas')
            ->count('id_kelas');
        
        // 2. Total Siswa (Dari siswa_kelompok yang terhubung ke kelompok guru ini)
        $this->siswaCount = DB::table('siswa_kelompok')
            ->join('kelompok', 'siswa_kelompok.id_kelompok', '=', 'kelompok.id_kelompok')
            ->where('kelompok.id_guru', $guru->id_guru)
            ->distinct('siswa_kelompok.id_siswa')
            ->count('siswa_kelompok.id_siswa');
        
        // 3. Rata-rata Nilai
        $rataQuery = SesiHafalan::where('id_guru', $guru->id_guru)
            ->avg('nilai_rata');
        $this->rataRataNilai = $rataQuery ? number_format($rataQuery, 2) : '0';
        
        // 4. Sesi Bulan Ini
        $blnSekarang = Carbon::now()->month;
        $thnSekarang = Carbon::now()->year;
        $this->sesiBlnIni = SesiHafalan::where('id_guru', $guru->id_guru)
            ->whereMonth('tanggal_setor', $blnSekarang)
            ->whereYear('tanggal_setor', $thnSekarang)
            ->count();
        
        // 5. Chart Progress Hafalan Per Kelas
        $kelasProgress = DB::table('kelompok')
            ->join('kelas', 'kelompok.id_kelas', '=', 'kelas.id_kelas')
            ->where('kelompok.id_guru', $guru->id_guru)
            ->select('kelas.nama_kelas')
            ->distinct()
            ->pluck('kelas.nama_kelas');
        
        foreach ($kelasProgress as $kelas) {
            $this->chartProgressLabels[] = $kelas;
            // Placeholder progress (75% - 90%)
            $this->chartProgressData[] = rand(75, 90);
        }
        
        // 6. Chart Tren Nilai 7 Hari Terakhir
        $sevenDaysAgo = Carbon::now()->subDays(6);
        $trenData = SesiHafalan::where('id_guru', $guru->id_guru)
            ->where('tanggal_setor', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE(tanggal_setor) as tanggal'),
                DB::raw('AVG(skor_tajwid) as avg_tajwid'),
                DB::raw('AVG(skor_makhroj) as avg_makhroj'),
                DB::raw('AVG(skor_kelancaran) as avg_kelancaran')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        $namaBulan = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Ming'];
        $hari = 0;
        
        foreach ($trenData as $data) {
            $this->chartTrenLabels[] = $namaBulan[$hari];
            $this->chartTrenTajwid[] = round($data->avg_tajwid ?? 0, 2);
            $this->chartTrenMakhroj[] = round($data->avg_makhroj ?? 0, 2);
            $this->chartTrenKelancaran[] = round($data->avg_kelancaran ?? 0, 2);
            $hari++;
        }
        
        // 7. Chart Distribusi Nilai Siswa
        $totalSesi = SesiHafalan::where('id_guru', $guru->id_guru)->count();
        
        if ($totalSesi > 0) {
            $sangat_baik = SesiHafalan::where('id_guru', $guru->id_guru)
                ->where('nilai_rata', '>=', 90)
                ->count();
            $baik = SesiHafalan::where('id_guru', $guru->id_guru)
                ->whereBetween('nilai_rata', [80, 89])
                ->count();
            $cukup = SesiHafalan::where('id_guru', $guru->id_guru)
                ->whereBetween('nilai_rata', [70, 79])
                ->count();
            $perlu_perbaikan = SesiHafalan::where('id_guru', $guru->id_guru)
                ->where('nilai_rata', '<', 70)
                ->count();
            
            $this->chartDistribusiLabels = ['Sangat Baik (90-100)', 'Baik (80-89)', 'Cukup (70-79)', 'Perlu Perbaikan (<70)'];
            $this->chartDistribusiData = [$sangat_baik, $baik, $cukup, $perlu_perbaikan];
        }
        
        // 8. Setoran Terbaru (5 terakhir)
        $this->setorTerbaru = SesiHafalan::where('id_guru', $guru->id_guru)
            ->join('siswa', 'sesi_hafalan.id_siswa', '=', 'siswa.id_siswa')
            ->join('surah', 'sesi_hafalan.id_surah_mulai', '=', 'surah.id_surah')
            ->select(
                'siswa.nama_siswa',
                'surah.nama_surah',
                'sesi_hafalan.tanggal_setor',
                'sesi_hafalan.nilai_rata'
            )
            ->orderBy('sesi_hafalan.tanggal_setor', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.guru.dashboard')
            ->layout('layouts.guru');
    }
}
