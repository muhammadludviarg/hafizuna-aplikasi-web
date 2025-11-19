<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $timestamps = false;

    protected $fillable = [
        'nama_siswa',
        'kode_siswa',
        'id_kelas',
        'nomor_surah_terakhir',
        'ayat_berikutnya',
    ];

    /**
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    /**
     * Relasi Many-to-Many ke Kelompok
     * Tabel pivot: siswa_kelompok
     */
    public function kelompok()
    {
        return $this->belongsToMany(
            Kelompok::class,
            'siswa_kelompok',    // ✅ Nama tabel pivot yang benar
            'id_siswa',          // FK untuk siswa
            'id_kelompok'        // FK untuk kelompok
        );
    }

    /**
     * Relasi ke Orang Tua
     */
    public function orangTua()
    {
        return $this->belongsToMany(
            OrangTua::class,
            'ortu_siswa',
            'id_siswa',
            'id_ortu'
        );
    }
}