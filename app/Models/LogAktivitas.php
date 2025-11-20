<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    
    protected $fillable = [
        'id_akun',
        'timestamp',
        'aktivitas',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // Relasi ke akun/user
    public function akun()
    {
        return $this->belongsTo(User::class, 'id_akun', 'id_akun');
    }
}