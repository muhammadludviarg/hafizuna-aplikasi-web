<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SesiHafalan extends Model
{
    use HasFactory;
    protected $table = 'sesi_hafalan';
    protected $primaryKey = 'id_sesi';
    public $timestamps = false;
    protected $fillable = [
        'id_siswa',
        'id_surah_mulai',
        'ayat_mulai',
        'id_surah_selesai',
        'ayat_selesai',
        'tanggal_setor',
        'proporsi_tajwid',
        'proporsi_makhroj',
        'proporsi_kelancaran',
        'skor_tajwid',
        'skor_makhroj',
        'skor_kelancaran',
        'grade_tajwid',
        'grade_makhroj',
        'grade_kelancaran',
        'nilai_rata',
        'id_guru'
    ];
    protected $casts = ['tanggal_setor' => 'datetime'];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
    public function surahMulai()
    {
        return $this->belongsTo(Surah::class, 'id_surah_mulai', 'id_surah');
    }
    public function surahSelesai()
    {
        return $this->belongsTo(Surah::class, 'id_surah_selesai', 'id_surah');
    }
    public function koreksi()
    {
        return $this->hasMany(Koreksi::class, 'id_sesi', 'id_sesi');
    }
}
