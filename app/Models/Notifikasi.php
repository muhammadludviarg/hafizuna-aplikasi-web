<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_sesi',
        'id_ortu',
        'waktu_kirim',
        'status',
        'pesan',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];
}