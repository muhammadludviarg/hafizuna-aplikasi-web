<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\SesiHafalan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $totalGuru;
    public $totalSiswa;
    public $totalKelas;
    public $rataRataNilai;
    
    public $chartKelasLabels = [];
    public $chartKelasData = [];
    public $chartAktivitasLabels = [];
    public $chartAktivitasData = [];
    
    public $radarAspekLabels = ['Tajwid', 'Makhroj', 'Kelancaran'];
    public $radarAspekData = [];
    
    public $guruPerformaLabels = [];
    public $guruPerformaAvgNilai = [];
    public $guruPerformaTotalSesi = [];
    
    public $aktivitasTerbaru = [];

    public function mount()
    {
        // 1. Total Statistics
        $this->totalGuru = Guru::count();
        $this->totalSiswa = Siswa::count();
        $this->totalKelas = Kelas::count();
        
        $rataRataQuery = DB::table('sesi_hafalan')
            ->select(DB::raw('avg(nilai_rata) as rata_rata_total'))
            ->first();

        $rataRata = $rataRataQuery->rata_rata_total ?? 0;
        $this->rataRataNilai = number_format($rataRata, 2);

        // 2. Chart: Siswa per Kelas
        $siswaPerKelas = Kelas::withCount('siswa')->get();
        
        foreach ($siswaPerKelas as $kelas) {
            $this->chartKelasLabels[] = $kelas->nama_kelas;
            $this->chartKelasData[] = $kelas->siswa_count;
        }

        // 3. Chart: Monthly Activity Trend
        $aktivitas = DB::table('sesi_hafalan')
            ->select(
                DB::raw('COUNT(*) as count'), 
                DB::raw('MONTH(tanggal_setor) as month')
            )
            ->whereYear('tanggal_setor', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $dataBulanan = array_fill(1, 12, 0);
        
        foreach ($aktivitas as $data) {
            $dataBulanan[$data->month] = $data->count;
        }

        $this->chartAktivitasLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $this->chartAktivitasData = array_values($dataBulanan);
        
        // 4. Radar Chart: Analisis Aspek Penilaian (Average score dari ketiga aspek)
        $aspekAnalisis = DB::table('sesi_hafalan')
            ->select(
                DB::raw('AVG(skor_tajwid) as tajwid'),
                DB::raw('AVG(skor_makhroj) as makhroj'),
                DB::raw('AVG(skor_kelancaran) as kelancaran')
            )
            ->first();
        
        $this->radarAspekData = [
            round($aspekAnalisis->tajwid ?? 0, 2),
            round($aspekAnalisis->makhroj ?? 0, 2),
            round($aspekAnalisis->kelancaran ?? 0, 2)
        ];
        
        // 5. Bar Chart: Performa Guru (Avg Nilai dan Total Sesi per guru)
        $guruPerforma = DB::table('guru')
            ->leftJoin('sesi_hafalan', 'guru.id_guru', '=', 'sesi_hafalan.id_guru')
            ->leftJoin('akun', 'guru.id_akun', '=', 'akun.id_akun')
            ->select(
                'guru.id_guru',
                'akun.nama_lengkap',
                DB::raw('AVG(sesi_hafalan.nilai_rata) as avg_nilai'),
                DB::raw('COUNT(sesi_hafalan.id_sesi) as total_sesi')
            )
            ->groupBy('guru.id_guru', 'akun.nama_lengkap')
            ->orderBy('avg_nilai', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($guruPerforma as $guru) {
            $this->guruPerformaLabels[] = $guru->nama_lengkap;
            $this->guruPerformaAvgNilai[] = round($guru->avg_nilai ?? 0, 2);
            $this->guruPerformaTotalSesi[] = $guru->total_sesi ?? 0;
        }
        
        // 6. Aktivitas Terbaru dengan proper JOIN ke akun table untuk mendapat nama guru yang benar
        $this->aktivitasTerbaru = DB::table('sesi_hafalan')
            ->join('siswa', 'sesi_hafalan.id_siswa', '=', 'siswa.id_siswa')
            ->join('guru', 'sesi_hafalan.id_guru', '=', 'guru.id_guru')
            ->join('akun as guru_akun', 'guru.id_akun', '=', 'guru_akun.id_akun')
            ->join('surah as surah_mulai', 'sesi_hafalan.id_surah_mulai', '=', 'surah_mulai.id_surah')
            ->join('surah as surah_selesai', 'sesi_hafalan.id_surah_selesai', '=', 'surah_selesai.id_surah')
            ->select(
                'siswa.nama_siswa',
                'guru_akun.nama_lengkap as guru_nama',
                'surah_mulai.nama_surah as surah_mulai_nama',
                'surah_selesai.nama_surah as surah_selesai_nama',
                'sesi_hafalan.tanggal_setor',
                'sesi_hafalan.nilai_rata'
            )
            ->orderBy('sesi_hafalan.tanggal_setor', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($sesi, $index) {
                return [
                    'index' => $index + 1,
                    'nama_siswa' => $sesi->nama_siswa ?? 'N/A',
                    'surah' => ($sesi->surah_mulai_nama ?? 'N/A') . ' • ' . ($sesi->surah_selesai_nama ?? 'N/A'),
                    'guru' => $sesi->guru_nama ?? 'N/A',
                    'tanggal' => $sesi->tanggal_setor ? Carbon::parse($sesi->tanggal_setor)->format('d M Y') : 'N/A',
                    'nilai' => round($sesi->nilai_rata ?? 0, 0),
                    'warna_avatar' => ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'][$index] ?? 'bg-blue-500'
                ];
            });
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
