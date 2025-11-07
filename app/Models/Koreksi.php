<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Koreksi extends Model {
    use HasFactory;
    protected $table = 'koreksi';
    protected $primaryKey = 'id_koreksi';
    public $timestamps = false;
    protected $fillable = ['id_sesi', 'id_ayat', 'kata_ke', 'kategori_kesalahan', 'catatan'];
    public function sesi() { return $this->belongsTo(SesiHafalan::class, 'id_sesi'); }
    public function ayat() { return $this->belongsTo(Ayat::class, 'id_ayat'); }
}