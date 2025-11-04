<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Koreksi extends Model
{
    use HasFactory;

    protected $table = 'koreksi';
    protected $primaryKey = 'id_koreksi';
    public $timestamps = false;

    protected $fillable = [
        'id_sesi',
        'id_ayat',
        'kata_ke',
        'kata_arab',
        'jenis_kesalahan',
        'catatan',
    ];
}