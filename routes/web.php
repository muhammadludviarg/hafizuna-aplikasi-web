<?php

use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\InputNilai;
use App\Http\Controllers\ProfileController;
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
        return redirect()->route('guru.dashboard'); // (Aktifkan nanti)
        //return view('dashboard'); // (Sementara)
    }

    if ($user->hasRole('ortu')) {
        // return redirect()->route('ortu.dashboard'); // (Aktifkan nanti)
        return view('dashboard'); // (Sementara)
    }

    // Fallback jika user tidak punya role
    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUP GURU ---
// --- GRUP GURU (YANG WAJIB LOGIN) ---
Route::prefix('guru')->middleware(['auth'])->name('guru.')->group(function () {
    Route::get('/dashboard', GuruDashboard::class)->name('dashboard');
    // Pindahkan rute input-nilai dari sini
});

// --- RUTE DEVELOPMENT (TIDAK PERLU LOGIN) ---
// Pindahkan rute input-nilai ke sini untuk tes
Route::get('/guru/input-nilai-dev', InputNilai::class)->name('guru.input-nilai');


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
