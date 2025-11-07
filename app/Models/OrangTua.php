<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OrangTua extends Model {
    use HasFactory;
    protected $table = 'orang_tua';
    protected $primaryKey = 'id_ortu';
    public $timestamps = false;
    protected $fillable = ['no_hp', 'id_akun'];
    public function akun() { return $this->belongsTo(User::class, 'id_akun'); }
    public function siswa() { return $this->hasMany(Siswa::class, 'id_ortu'); }
}