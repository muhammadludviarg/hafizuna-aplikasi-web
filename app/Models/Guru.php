<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Guru extends Model {
    use HasFactory;
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $timestamps = false;
    protected $fillable = ['id_akun', 'no_hp'];
    public function akun() { return $this->belongsTo(User::class, 'id_akun', 'id_akun'); }
}