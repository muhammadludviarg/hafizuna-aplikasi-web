<?php

namespace App\Helpers;

use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Catat aktivitas user dengan timezone Indonesia (WIB)
     * 
     * @param string $aktivitas Deskripsi aktivitas
     * @param int|null $idAkun ID akun (opsional, default dari Auth)
     * @return void
     */
    public static function log($aktivitas, $idAkun = null)
    {
        try {
            // Jika id_akun tidak diberikan, ambil dari user yang login
            if ($idAkun === null && Auth::check()) {
                $idAkun = Auth::user()->id_akun;
            }

            // Jika masih null, skip logging
            if ($idAkun === null) {
                return;
            }

            // Gunakan Carbon dengan timezone Asia/Jakarta (WIB)
            $timestamp = Carbon::now('Asia/Jakarta');

            LogAktivitas::create([
                'id_akun' => $idAkun,
                'timestamp' => $timestamp,
                'aktivitas' => $aktivitas,
            ]);
        } catch (\Exception $e) {
            // Silent fail - jangan ganggu proses utama
            Log::error('Failed to log activity', [
                'aktivitas' => $aktivitas,
                'id_akun' => $idAkun,
                'error' => $e->getMessage(),
            ]);
        }
    }
}