<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SesiHafalan;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get statistics
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        
        // Calculate average rating from sesi hafalan
        $rataRataNilai = SesiHafalan::avg('nilai_rata') ?? 0;
        
        // Get statistics per class
        $statistikPerKelas = DB::table('kelas')
            ->leftJoin('siswa', 'kelas.id_kelas', '=', 'siswa.id_kelas')
            ->select('kelas.nama_kelas', DB::raw('COUNT(siswa.id_siswa) as jumlah_siswa'))
            ->groupBy('kelas.id_kelas', 'kelas.nama_kelas')
            ->get();
        
        // Get average grade per class
        $rataPerKelas = DB::table('kelas')
            ->leftJoin('siswa', 'kelas.id_kelas', '=', 'siswa.id_kelas')
            ->leftJoin('sesi_hafalan', 'siswa.id_siswa', '=', 'sesi_hafalan.id_siswa')
            ->select('kelas.nama_kelas', DB::raw('AVG(sesi_hafalan.nilai_rata) as rata_nilai'))
            ->groupBy('kelas.id_kelas', 'kelas.nama_kelas')
            ->get();
        
        // Get monthly activity trend (Tren Aktivitas Bulanan)
        $trendAktivitas = DB::table('sesi_hafalan')
            ->select(
                DB::raw('MONTH(tanggal_setor) as bulan'),
                DB::raw('COUNT(*) as jumlah_sesi'),
                DB::raw('AVG(nilai_rata) as rata_rata_nilai')
            )
            ->whereYear('tanggal_setor', date('Y'))
            ->groupBy(DB::raw('MONTH(tanggal_setor)'))
            ->orderBy(DB::raw('MONTH(tanggal_setor)'))
            ->get();
        
        // Get aspect analysis (Analisis Aspek Pemilihan)
        $aspekAnalisis = [
            'tajwid' => [
                'akual' => round(SesiHafalan::avg('skor_tajwid') ?? 0),
                'target' => 95
            ],
            'makhroj' => [
                'akual' => round(SesiHafalan::avg('skor_makhroj') ?? 0),
                'target' => 95
            ],
            'kelancaran' => [
                'akual' => round(SesiHafalan::avg('skor_kelancaran') ?? 0),
                'target' => 95
            ]
        ];
        
        // Get teacher performance
        $performaGuru = DB::table('guru')
            ->join('akun', 'guru.id_akun', '=', 'akun.id_akun')
            ->leftJoin('sesi_hafalan', 'guru.id_guru', '=', 'sesi_hafalan.id_guru')
            ->select(
                'akun.nama_lengkap',
                DB::raw('COUNT(sesi_hafalan.id_sesi) as total_sesi'),
                DB::raw('AVG(sesi_hafalan.nilai_rata) as rata_nilai')
            )
            ->groupBy('guru.id_guru', 'akun.nama_lengkap')
            ->get();
        
        // Get recent activity
        $aktivitasTerbaru = LogAktivitas::with('akun')
            ->orderBy('timestamp', 'desc')
            ->limit(5)
            ->get();
        
        // Alternative if log_aktivitas is empty: get recent sesi hafalan
        if ($aktivitasTerbaru->isEmpty()) {
            $aktivitasTerbaru = DB::table('sesi_hafalan')
                ->join('siswa', 'sesi_hafalan.id_siswa', '=', 'siswa.id_siswa')
                ->join('guru', 'sesi_hafalan.id_guru', '=', 'guru.id_guru')
                ->join('akun', 'guru.id_akun', '=', 'akun.id_akun')
                ->select('akun.nama_lengkap', 'siswa.nama_siswa', 'sesi_hafalan.tanggal_setor')
                ->orderBy('sesi_hafalan.tanggal_setor', 'desc')
                ->limit(5)
                ->get();
        }
        
        $user = Auth::user();
        
        return view('dashboard.index', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'rataRataNilai',
            'statistikPerKelas',
            'rataPerKelas',
            'trendAktivitas',
            'aspekAnalisis',
            'performaGuru',
            'aktivitasTerbaru',
            'user'
        ));
    }
}
