<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHafalanKelompok extends Model
{
    use HasFactory;
    protected $table = 'target_hafalan_kelompok';
    protected $primaryKey = 'id_target';
    public $timestamps = false;

    // SESUAIKAN $fillable
    protected $fillable = [
        'periode', 
        'tanggal_mulai',
        'tanggal_selesai', 
        'id_kelompok', 
        'id_surah_awal', // <-- BARU
        'id_surah_akhir', // <-- BARU
        'id_admin'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date', 
        'tanggal_selesai' => 'date'
    ];
    
    public function kelompok() 
    { 
        return $this->belongsTo(Kelompok::class, 'id_kelompok'); 
    }

    public function surahAwal() 
    { 
        return $this->belongsTo(Surah::class, 'id_surah_awal'); 
    }
    
    public function surahAkhir() 
    { 
        return $this->belongsTo(Surah::class, 'id_surah_akhir'); 
    }

    public function admin() 
    { 
        return $this->belongsTo(Admin::class, 'id_admin'); 
    }
}