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
    // Di dalam class Siswa (app/Models/Siswa.php)

    /**
     * Mendapatkan data riwayat kelompok siswa.
     */
    public function siswaKelompok()
    {
        // Satu Siswa bisa memiliki banyak riwayat di SiswaKelompok
        return $this->hasMany(SiswaKelompok::class, 'id_siswa', 'id_siswa');
    }
}