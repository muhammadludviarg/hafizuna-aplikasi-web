<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TargetHafalanKelompok extends Model {
    use HasFactory;
    protected $table = 'target_hafalan_kelompok';
    protected $primaryKey = 'id_target';
    public $timestamps = false;
    protected $fillable = [
        'ayat_awal', 'ayat_akhir', 'periode', 'tanggal_mulai',
        'tanggal_selesai', 'id_kelompok', 'id_surah', 'id_admin'
    ];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];
    public function kelompok() { return $this->belongsTo(Kelompok::class, 'id_kelompok'); }
    public function surah() { return $this->belongsTo(Surah::class, 'id_surah'); }
    public function admin() { return $this->belongsTo(Admin::class, 'id_admin'); }
}