<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrtuSiswa extends Model
{
    use HasFactory;

    protected $table = 'ortu_siswa';
    protected $primaryKey = 'id_relasi';
    public $timestamps = false;

    protected $fillable = [
        'id_ortu',
        'id_siswa',
        'hubungan',
    ];
}