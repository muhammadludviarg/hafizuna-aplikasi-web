<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';
    protected $primaryKey = 'id_periode';
    protected $fillable = ['tahun_ajaran', 'semester', 'label', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function targetHafalan()
    {
        return $this->hasMany(TargetHafalanKelompok::class, 'id_periode');
    }
}
