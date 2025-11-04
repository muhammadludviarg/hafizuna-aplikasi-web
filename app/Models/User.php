<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Beritahu Laravel ini adalah tabel 'akun'
     */
    protected $table = 'akun';

    /**
     * Beritahu Laravel ini adalah primary key-nya
     */
    protected $primaryKey = 'id_akun';

    /**
     * Atur kolom timestamp
     */
    const CREATED_AT = 'dibuat_pada'; // Sesuai .sql
    const UPDATED_AT = null; // Kita tidak punya 'updated_at'

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'sandi_hash',
        'status',
        'role', // Jangan lupa tambahkan 'role' di migrasi 'akun'
    ];

    /**
     * Kolom yang disembunyikan
     */
    protected $hidden = [
        'sandi_hash',
        'remember_token',
    ];

    /**
     * Casts untuk tipe data
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'dibuat_pada' => 'datetime',
    ];

    /**
     * Ambil password untuk autentikasi
     */
    public function getAuthPassword()
    {
        return $this->sandi_hash;
    }
}