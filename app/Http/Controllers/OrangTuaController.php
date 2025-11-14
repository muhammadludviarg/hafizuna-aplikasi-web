<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class OrangTuaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil semua anak dari orang tua ini
        $children = Siswa::where('user_id', $user->id)->get();
        
        // Jika tidak ada anak, redirect ke halaman sebelumnya
        if ($children->isEmpty()) {
            return view('ortu.dashboard', ['children' => $children, 'activeChild' => null]);
        }
        
        // Default tampilkan anak pertama
        $activeChild = $children->first();
        
        return view('ortu.dashboard', [
            'children' => $children,
            'activeChild' => $activeChild,
            'totalSesi' => 5,
            'rataRataNilai' => 89,
            'progressHafalan' => 67,
            'sesiTerakhir' => [
                ['surah' => 'Abasa', 'nilai' => 88],
                ['surah' => 'Abasa', 'nilai' => 86],
            ]
        ]);
    }
    
    public function viewChild($childId)
    {
        $user = Auth::user();
        $child = Siswa::where('id', $childId)->where('user_id', $user->id)->firstOrFail();
        $children = Siswa::where('user_id', $user->id)->get();
        
        return view('ortu.dashboard', [
            'children' => $children,
            'activeChild' => $child,
            'totalSesi' => 5,
            'rataRataNilai' => 89,
            'progressHafalan' => 67,
            'sesiTerakhir' => [
                ['surah' => 'Abasa', 'nilai' => 88],
                ['surah' => 'Abasa', 'nilai' => 86],
            ]
        ]);
    }
}
