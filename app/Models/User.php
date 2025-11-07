<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'sandi_hash',
        'status',
    ];

    protected $hidden = [
        'sandi_hash',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'dibuat_pada' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->sandi_hash;
    }

    // --- Relasi untuk Cek Role (Cara Baru) ---
    public function admin() { return $this->hasOne(Admin::class, 'id_akun'); }
    public function guru() { return $this->hasOne(Guru::class, 'id_akun'); }
    public function ortu() { return $this->hasOne(OrangTua::class, 'id_akun', 'id_akun'); }

    // --- Fungsi Cek Role ---
    public function hasRole($role)
    {
        $role = strtolower($role);
        if ($role === 'admin' && $this->admin()->exists()) return true;
        if ($role === 'guru' && $this->guru()->exists()) return true;
        if ($role === 'ortu' && $this->ortu()->exists()) return true;
        return false;
    }
}