<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiHafalan extends Model
{
    use HasFactory;

    protected $table = 'sesi_hafalan';
    protected $primaryKey = 'id_sesi';
    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'id_guru',
        'id_surah_mulai',
        'ayat_mulai',
        'id_surah_selesai',
        'ayat_selesai',
        'tanggal_setor',
        'nilai_tajwid',
        'nilai_makhroj',
        'nilai_kelancaran',
        'nilai_rata',
    ];

    protected $casts = [
        'tanggal_setor' => 'datetime',
    ];
}