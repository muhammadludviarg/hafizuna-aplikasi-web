<?php

namespace App\Models;

// 1. IMPORT CONTRACT CanResetPassword
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword;

// 2. IMPLEMENTASI CONTRACT
class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $incrementing = true; // Tambahkan ini jika id_akun adalah auto-increment
    protected $keyType = 'int'; // Ganti dengan 'string' jika id_akun bukan integer

    // Perbaikan: Laravel secara default mencari 'password', kita ganti dengan sandi_hash
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null; // Karena Anda menonaktifkan UPDATED_AT

    protected $fillable = [
        'nama_lengkap',
        'email',
        'sandi_hash', // Nama kolom password yang Anda gunakan
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
        // Tambahkan casting untuk sandi_hash jika Anda ingin memanfaatkan otomatis hashing dari Laravel
        'sandi_hash' => 'hashed',
    ];

    /**
     * Mengarahkan Laravel ke kolom password kustom Anda.
     */
    public function getAuthPassword()
    {
        return $this->sandi_hash;
    }

    /**
     * Metode yang WAJIB diimplementasikan untuk reset password.
     * Digunakan oleh fitur reset password untuk mendapatkan alamat email.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    // --- Relasi dan Fungsi Cek Role Anda (Dibiarkan) ---
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_akun');
    }
    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_akun');
    }
    public function ortu()
    {
        return $this->hasOne(OrangTua::class, 'id_akun', 'id_akun');
    }

    public function hasRole($role)
    {
        $role = strtolower($role);
        if ($role === 'admin' && $this->admin()->exists())
            return true;
        if ($role === 'guru' && $this->guru()->exists())
            return true;
        if ($role === 'ortu' && $this->ortu()->exists())
            return true;
        return false;
    }

    public function sendPasswordResetNotification($token)
    {
        // 'CustomResetPassword' adalah nama kelas notifikasi yang telah Anda buat
        $this->notify(new CustomResetPassword($token));
    }
}