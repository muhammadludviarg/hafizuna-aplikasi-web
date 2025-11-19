<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';
    protected $primaryKey = 'id_kelompok';
    public $timestamps = false;

    protected $fillable = [
        'nama_kelompok',
        'id_kelas',
        'id_guru',
    ];

    /**
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    /**
     * Relasi ke Guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    /**
     * Relasi Many-to-Many ke Siswa
     * Tabel pivot: siswa_kelompok
     */
    public function siswa()
    {
        return $this->belongsToMany(
            Siswa::class,
            'siswa_kelompok',    // ✅ Nama tabel pivot yang benar
            'id_kelompok',       // FK untuk kelompok
            'id_siswa'           // FK untuk siswa
        );
    }
}