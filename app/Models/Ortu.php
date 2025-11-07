<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ortu extends Model
{
    use HasFactory;

    protected $table = 'orang_tua'; // Sesuaikan dengan nama tabel di .sql
    protected $primaryKey = 'id_ortu';
    public $timestamps = false;

    protected $fillable = [
        'id_akun',
        'no_hp',
    ];

    /**
     * Mendapatkan data akun (User) yang terkait dengan Ortu.
     */
    public function akun()
    {
        return $this->belongsTo(User::class, 'id_akun', 'id_akun');
    }
}