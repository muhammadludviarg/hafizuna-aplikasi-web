<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $table = 'surah';
    protected $primaryKey = 'id_surah';
    public $timestamps = false;

    protected $fillable = [
        'nomor_surah',
        'nama_surah',
        'jumlah_ayat',
        'tempat_turun',
    ];
}