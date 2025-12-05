<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportLaporanHafalanController;
use Illuminate\Support\Facades\Route;

// ADMIN COMPONENTS
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\DataAdmin;
use App\Livewire\Admin\KelolaGuru;
use App\Livewire\Admin\KelolaSiswa;
use App\Livewire\Admin\KelolaKelas;
use App\Livewire\Admin\KelolaKelompok;
use App\Livewire\Admin\PengaturanNilai;
use App\Livewire\Admin\TargetHafalan;
use App\Livewire\Admin\GantiPassword as AdminGantiPassword;
use App\Livewire\Admin\DataMaster;
use App\Livewire\Admin\LogAktivitasAdmin;
use App\Livewire\Admin\LaporanHafalan as AdminLaporanHafalan;

// GURU COMPONENTS
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\InputNilai;
use App\Livewire\Guru\ManajemenKelompok;
use App\Livewire\Guru\DetailKelompok;
use App\Livewire\Guru\LaporanHafalan;
use App\Livewire\Guru\GantiPassword;

// ORTU COMPONENTS
use App\Livewire\OrangTua\Dashboard as OrtuDashboard;
use App\Livewire\OrangTua\LaporanHafalan as OrtuLaporanHafalan;
use App\Livewire\OrangTua\GantiPassword as OrtuGantiPassword;

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
| RUTE EXPORT (Bisa diakses Admin, Guru, & Ortu)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Nama route kita hilangkan prefix 'admin.' nya

    Route::get('/export/laporan-hafalan/pdf/{kelasId}', [ExportLaporanHafalanController::class, 'exportPdf'])
        ->name('export.laporan-hafalan.pdf'); // Digunakan oleh Admin saja

    Route::get('/export/laporan-hafalan/excel/{kelasId}', [ExportLaporanHafalanController::class, 'exportExcel'])
        ->name('export.laporan-hafalan.excel'); // Digunakan oleh Admin saja

    Route::get('/export/laporan-hafalan/pdf-kelompok/{kelompokId}', [ExportLaporanHafalanController::class, 'exportPdfKelompok'])
        ->name('export.laporan-hafalan.pdf-kelompok');

    Route::get('/export/laporan-hafalan/excel-kelompok/{kelompokId}', [ExportLaporanHafalanController::class, 'exportExcelKelompok'])
        ->name('export.laporan-hafalan.excel-kelompok');

    // Rute Siswa & Sesi (Digunakan Admin, Guru, Ortu)
    Route::get('/export/laporan-hafalan/pdf-siswa/{siswaId}', [ExportLaporanHafalanController::class, 'exportPdfSiswa'])
        ->name('export.laporan-hafalan.pdf-siswa');

    Route::get('/export/laporan-hafalan/excel-siswa/{siswaId}', [ExportLaporanHafalanController::class, 'exportExcelSiswa'])
        ->name('export.laporan-hafalan.excel-siswa');

    Route::get('/export/sesi-setoran/pdf/{siswaId}/{surahId}', [ExportLaporanHafalanController::class, 'exportPdfSesi'])
        ->name('export.sesi-setoran.pdf');

    Route::get('/export/sesi-setoran/excel/{siswaId}/{surahId}', [ExportLaporanHafalanController::class, 'exportExcelSesi'])
        ->name('export.sesi-setoran.excel');
});

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
    */
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        }

        if ($user->hasRole('ortu')) {
            return redirect()->route('ortu.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Fallback jika tidak punya peran
        return view('dashboard');
    })->name('dashboard');

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

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/data-admin', DataAdmin::class)->name('data-admin');
    Route::get('/data-master', DataMaster::class)->name('data-master');
    Route::get('/kelola-guru', KelolaGuru::class)->name('kelola-guru');
    Route::get('/kelola-siswa', KelolaSiswa::class)->name('kelola-siswa');
    Route::get('/kelola-kelas', KelolaKelas::class)->name('kelola-kelas');
    Route::get('/kelola-kelompok', KelolaKelompok::class)->name('kelola-kelompok');

    Route::get('/pengaturan-nilai', PengaturanNilai::class)->name('pengaturan-nilai');
    Route::get('/target-hafalan', TargetHafalan::class)->name('target-hafalan');

    // ROUTE LOG AKTIVITAS - DIPERBAIKI (tanpa duplikat /admin)
    Route::get('/log-aktivitas', LogAktivitasAdmin::class)->name('log-aktivitas');

    Route::get('/ganti-password', AdminGantiPassword::class)->name('ganti-password');

    Route::get('/laporan-hafalan', AdminLaporanHafalan::class)->name('laporan-hafalan');

    Route::get('/laporan-hafalan', AdminLaporanHafalan::class)->name('laporan-hafalan');
});

/*
|--------------------------------------------------------------------------
| GRUP GURU (Dilindungi)
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', GuruDashboard::class)->name('dashboard');
    Route::get('/input-nilai', InputNilai::class)->name('input-nilai');
    Route::get('/laporan-hafalan', LaporanHafalan::class)->name('laporan-hafalan');
    Route::get('kelompok', ManajemenKelompok::class)->name('kelompok.index');
    Route::get('kelompok/{id}', DetailKelompok::class)->name('kelompok.detail');
    Route::get('/ganti-password', GantiPassword::class)->name('ganti-password');
});

/*
|--------------------------------------------------------------------------
| GRUP ORTU (Dilindungi)
|--------------------------------------------------------------------------
*/
Route::prefix('ortu')->name('ortu.')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', OrtuDashboard::class)->name('dashboard');
    Route::get('/laporan', OrtuLaporanHafalan::class)->name('laporan');
    Route::get('/ganti-password', OrtuGantiPassword::class)->name('ganti-password');

});
