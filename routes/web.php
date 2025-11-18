<?php

use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\InputNilai;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ADMIN COMPONENTS
use App\Livewire\Admin\DataAdmin;
use App\Livewire\Admin\KelolaGuru;
use App\Livewire\Admin\KelolaSiswa;
use App\Livewire\Admin\KelolaKelas;
use App\Livewire\Admin\KelolaKelompok as AdminKelolaKelompok;
use App\Livewire\Admin\PengaturanNilai;
use App\Livewire\Admin\TargetHafalan;
use App\Livewire\Admin\GantiPassword as AdminGantiPassword;
use App\Livewire\Admin\DataMaster;

// GURU COMPONENTS
use App\Livewire\Guru\ManajemenKelompok;
use App\Livewire\Guru\DetailKelompok;
use App\Livewire\Guru\LaporanHafalan;
use App\Livewire\Guru\GantiPassword;

/*
|--------------------------------------------------------------------------
| Rute Halaman Utama
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rute Autentikasi Bawaan Breeze
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Grup Rute yang Dilindungi (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rute Dashboard "Pintar" (Smart Redirect)
    |--------------------------------------------------------------------------
    | Ini adalah rute utama setelah login.
    | Rute ini akan mengecek role pengguna (via Model User) dan
    | mengarahkan mereka ke dashboard yang sesuai.
    */
    Route::get('/dashboard', function () {
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
    })->name('dashboard');

    // Rute Profil Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| GRUP ADMIN (Dilindungi)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute Fitur Admin
    Route::get('/data-admin', DataAdmin::class)->name('data-admin');
    Route::get('/data-master', DataMaster::class)->name('data-master');
    Route::get('/kelola-guru', KelolaGuru::class)->name('kelola-guru');
    Route::get('/kelola-siswa', KelolaSiswa::class)->name('kelola-siswa');
    Route::get('/kelola-kelas', KelolaKelas::class)->name('kelola-kelas');
    Route::get('/kelola-kelompok', AdminKelolaKelompok::class)->name('kelola-kelompok');

    // Rute Fitur Lama
    Route::get('/pengaturan-nilai', PengaturanNilai::class)->name('pengaturan-nilai');
    Route::get('/target-hafalan', TargetHafalan::class)->name('target-hafalan');

    // Rute Ganti Password Admin
    Route::get('/ganti-password', AdminGantiPassword::class)->name('ganti-password');
});

/*
|--------------------------------------------------------------------------
| GRUP GURU (Dilindungi)
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard Guru
    Route::get('/dashboard', GuruDashboard::class)->name('dashboard');

    // Rute Fitur Guru
    Route::get('/input-nilai', InputNilai::class)->name('input-nilai');
    Route::get('/laporan-hafalan', LaporanHafalan::class)->name('laporan-hafalan');

    // Rute Kelola Kelompok
    Route::get('kelompok', ManajemenKelompok::class)->name('kelompok.index');
    Route::get('kelompok/{id}', DetailKelompok::class)->name('kelompok.detail');

    // Rute Ganti Password Guru
    Route::get('/ganti-password', GantiPassword::class)->name('ganti-password');
});

/*
|--------------------------------------------------------------------------
| GRUP ORTU (Dilindungi)
|--------------------------------------------------------------------------
*/
Route::prefix('ortu')->name('ortu.')->middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Ortu (belum dibuat)
    Route::get('/dashboard', function () {
        return view('dashboard'); // Sementara
    })->name('dashboard');

    // Rute fitur ortu akan ditambahkan di sini
});