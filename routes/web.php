<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\PengaturanNilai;

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
        // return redirect()->route('guru.dashboard'); // (Aktifkan nanti)
        return view('dashboard'); // (Sementara)
    }

    if ($user->hasRole('ortu')) {
        // return redirect()->route('ortu.dashboard'); // (Aktifkan nanti)
        return view('dashboard'); // (Sementara)
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

        // (Kita akan buat ulang semua file ini satu per satu)
        Route::get('/dashboard', function () {
            return view('dashboard'); 
        })->name('dashboard');

        // Pengaturan Nilai
        Route::get('/pengaturan-nilai', App\Livewire\Admin\PengaturanNilai::class)
            ->name('pengaturan-nilai');

    });

    // --- GRUP GURU ---
    Route::prefix('guru')->name('guru.')->group(function () {
        // (Nanti rute guru di sini)
    });

    // --- GRUP ORTU ---
    Route::prefix('ortu')->name('ortu.')->group(function () {
        // (Nanti rute ortu di sini)
    });

});


// Rute Autentikasi Bawaan Breeze
require __DIR__ . '/auth.php';
