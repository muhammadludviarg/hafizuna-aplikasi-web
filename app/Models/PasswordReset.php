<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PasswordReset extends Model {
    use HasFactory;
    protected $table = 'password_resets';
    protected $primaryKey = 'id_reset';
    public $timestamps = false;
    protected $fillable = ['id_akun', 'token', 'kadaluarsa_pada'];
    protected $casts = ['kadaluarsa_pada' => 'datetime'];
}