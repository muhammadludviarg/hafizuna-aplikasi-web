<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Siswa extends Model {
    use HasFactory;
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $timestamps = false;
    protected $fillable = ['nama_siswa', 'kode_siswa', 'id_kelas', 'id_ortu'];
    public function kelas() { return $this->belongsTo(Kelas::class, 'id_kelas'); }
    public function ortu() { return $this->belongsTo(OrangTua::class, 'id_ortu'); }

    /**
     * Relasi ke Kelompok (Many-to-Many)
     */
    public function kelompok()
    {
        return $this->belongsToMany(Kelompok::class, 'siswa_kelompok', 'id_siswa', 'id_kelompok');
    }

    /**
     * Relasi ke Model Pivot SiswaKelompok (HASMANY) - Digunakan oleh InputNilai.php
     */
    public function siswaKelompok()
        {
        return $this->hasMany(SiswaKelompok::class, 'id_siswa', 'id_siswa');
    }
}