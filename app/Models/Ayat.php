<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{
    use HasFactory;

    protected $table = 'ayat';
    protected $primaryKey = 'id_ayat';
    public $timestamps = false;

    protected $fillable = [
        'id_surah',
        'nomor_ayat',
        'teks_arab',
        'terjemahan',
    ];
}