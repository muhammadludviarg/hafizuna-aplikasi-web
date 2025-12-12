<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHafalanKelompok extends Model
{
    use HasFactory;

    protected $table = 'target_hafalan_kelompok';
    protected $primaryKey = 'id_target';

    protected $fillable = [
        'id_surah_awal',
        'id_surah_akhir',
        'id_kelompok',
        'id_admin',
        'id_periode', 
    ];

    // Relasi ke Tabel Kelompok
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok', 'id_kelompok');
    }

    // Relasi ke Tabel Surah (Awal)
    public function surahAwal()
    {
        return $this->belongsTo(Surah::class, 'id_surah_awal', 'id_surah');
    }

    // Relasi ke Tabel Surah (Akhir)
    public function surahAkhir()
    {
        return $this->belongsTo(Surah::class, 'id_surah_akhir', 'id_surah');
    }

    // Relasi ke Tabel Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }
}