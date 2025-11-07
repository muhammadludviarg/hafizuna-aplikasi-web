<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SiswaKelompok extends Model {
    use HasFactory;
    protected $table = 'siswa_kelompok';
    protected $primaryKey = 'id_siswa_kelompok';
    public $timestamps = false;
    protected $fillable = ['id_siswa', 'tgl_mulai', 'tgl_selesai', 'id_kelompok'];
    protected $casts = ['tgl_mulai' => 'date', 'tgl_selesai' => 'date'];
    public function siswa() { return $this->belongsTo(Siswa::class, 'id_siswa'); }
    public function kelompok() { return $this->belongsTo(Kelompok::class, 'id_kelompok'); }
}