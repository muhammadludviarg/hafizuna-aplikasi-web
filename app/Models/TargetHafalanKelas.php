<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHafalanKelas extends Model
{
    use HasFactory;

    protected $table = 'target_hafalan_kelas';
    protected $primaryKey = 'id_target';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'id_surah',
        'ayat_awal',
        'ayat_akhir',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}