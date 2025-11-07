<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class LogAktivitas extends Model {
    use HasFactory;
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    protected $fillable = ['id_akun', 'timestamp', 'aktivitas'];
    protected $casts = ['timestamp' => 'datetime'];
    public function akun() { return $this->belongsTo(User::class, 'id_akun'); }
}