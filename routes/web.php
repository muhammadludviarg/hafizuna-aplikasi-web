<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\OrangTuaController;
use Illuminate\Support\Facades\Route;

// --- Impor Semua Komponen Livewire Anda di Sini ---

/*
|--------------------------------------------------------------------------
| Rute Halaman Utama
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rute Dashboard "Pintar" (Smart Redirect)
|--------------------------------------------------------------------------
| Ini adalah rute utama setelah login.
| Rute ini akan mengecek role pengguna (via Model User) dan
| mengarahkan mereka ke dashboard yang sesuai.
*/
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    // Gunakan fungsi hasRole() yang sudah kita buat di app/Models/User.php
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('guru')) {
        return redirect()->route('guru.dashboard');
    }

    if ($user->hasRole('ortu')) {
        return redirect()->route('ortu.dashboard');
    }

    // Fallback jika user tidak punya role
    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Grup Rute yang Dilindungi (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Rute Profil Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GRUP ADMIN ---
    // (Awalan URL: /admin/... , Awalan Nama: admin. ...)
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/data-master', function () {
            return view('admin.data-master');
        })->name('data-master');
        
        Route::get('/target-hafalan', function () {
            return view('admin.target-hafalan');
        })->name('target-hafalan');
        
        Route::get('/kelas-kelompok', function () {
            return view('admin.kelas-kelompok');
        })->name('kelas-kelompok');
        
        Route::get('/laporan', function () {
            return view('admin.laporan');
        })->name('laporan');

    });

    // --- GRUP GURU ---
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    });

    // --- GRUP ORTU ---
    Route::prefix('ortu')->name('ortu.')->group(function () {
        Route::get('/dashboard', [OrangTuaController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/child/{childId}', [OrangTuaController::class, 'viewChild'])->name('view-child');
    });

});


// Rute Autentikasi Bawaan Breeze
require __DIR__ . '/auth.php';
