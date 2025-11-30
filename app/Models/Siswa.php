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
        'id_ortu',  // ✅ Pastikan ini ada
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
     */
    public function kelompok()
    {
        return $this->belongsToMany(
            Kelompok::class,
            'siswa_kelompok',
            'id_siswa',
            'id_kelompok'
        )->withPivot('tgl_mulai', 'tgl_selesai');
    }

    public function ortu()
    {
        return $this->belongsTo(OrangTua::class, 'id_ortu', 'id_ortu');
    }
}