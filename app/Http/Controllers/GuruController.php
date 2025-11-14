<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\SesiHafalan;
use Carbon\Carbon;

class GuruController extends Controller
{
    public function dashboard()
    {
        $guru = auth()->user()->guru ?? Guru::where('user_id', auth()->id())->first();
        
        // Kelas yang diampu
        $kelasD = $guru->kelas ?? collect();
        $totalKelasDiampu = $guru->kelas_count ?? 0;
        
        // Total siswa bimbingan aktif
        $totalSiswa = $guru->siswa()->count();
        
        // Rata-rata nilai
        $rataRataNilai = SesiHafalan::whereHas('siswa', function($q) use ($guru) {
            $q->where('guru_id', $guru->id);
        })->avg('nilai') ?? 0;
        
        // Total sesi bulan ini
        $totalSesiBulanIni = SesiHafalan::whereHas('siswa', function($q) use ($guru) {
            $q->where('guru_id', $guru->id);
        })->whereYear('tanggal_sesi', Carbon::now()->year)
            ->whereMonth('tanggal_sesi', Carbon::now()->month)
            ->count();
        
        // Progres hafalan per kelas
        $progresPerKelas = $guru->kelas()
            ->with(['siswa' => function($q) {
                $q->withCount('sesiHafalan');
            }])
            ->get()
            ->map(function($kelas) {
                $targetSesi = 10; // Target default
                $aktualSesi = SesiHafalan::whereHas('siswa', function($q) use ($kelas) {
                    $q->where('kelas_id', $kelas->id);
                })->count();
                
                return [
                    'nama_kelas' => $kelas->nama_kelas,
                    'progres' => $targetSesi > 0 ? round(($aktualSesi / $targetSesi) * 100, 0) : 0,
                    'target' => 100
                ];
            });
        
        // Tren nilai 7 hari terakhir per aspek
        $tren = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $tren[$date->format('D')] = [
                'Kelancaran' => SesiHafalan::whereHas('siswa', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })->whereDate('tanggal_sesi', $date)->avg('nilai_kelancaran') ?? 0,
                'Makhroj' => SesiHafalan::whereHas('siswa', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })->whereDate('tanggal_sesi', $date)->avg('nilai_makhroj') ?? 0,
                'Tajwid' => SesiHafalan::whereHas('siswa', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })->whereDate('tanggal_sesi', $date)->avg('nilai_tajwid') ?? 0,
            ];
        }
        
        return view('guru.dashboard', [
            'guru' => $guru,
            'totalKelasDiampu' => $totalKelasDiampu,
            'kelasD' => $kelasD,
            'totalSiswa' => $totalSiswa,
            'rataRataNilai' => round($rataRataNilai, 0),
            'totalSesiBulanIni' => $totalSesiBulanIni,
            'progresPerKelas' => $progresPerKelas,
            'trenNilai' => $tren,
        ]);
    }
}
