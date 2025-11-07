<?php

use App\Livewire\Admin\KelolaKelas;
use App\Livewire\Admin\KelolaGuru;
use App\Livewire\Admin\KelolaOrtu;
use App\Livewire\Admin\KelolaSiswa;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->group(function () {
        Route::get('/kelola-kelas', KelolaKelas::class)->name('admin.kelas');
        Route::get('/kelola-guru', KelolaGuru::class)->name('admin.guru');
        Route::get('/kelola-ortu', KelolaOrtu::class)->name('admin.ortu');
        Route::get('/kelola-siswa', KelolaSiswa::class)->name('admin.siswa');
        
        // Nanti rute admin lain (siswa, guru) masuk sini
    });

require __DIR__.'/auth.php';
