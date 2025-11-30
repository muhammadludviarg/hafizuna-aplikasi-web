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
        //'tahun_ajaran', 
    ];
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_kelompok', 'id_kelompok', 'id_siswa')
                    ->withPivot('tgl_mulai', 'tgl_selesai');
    }
}
