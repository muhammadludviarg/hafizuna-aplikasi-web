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
    protected $fillable = ['waktu_kirim', 'status', 'pesan', 'id_ortu', 'id_sesi'];
    protected $casts = ['waktu_kirim' => 'datetime'];
    public function ortu()
    {
        return $this->belongsTo(OrangTua::class, 'id_ortu');
    }
    public function sesi()
    {
        return $this->belongsTo(SesiHafalan::class, 'id_sesi');
    }
}