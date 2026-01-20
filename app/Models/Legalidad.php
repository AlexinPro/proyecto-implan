<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legalidad extends Model
{
    protected $table = 'legalidad';
    
    protected $fillable = [
        'consejo_id',
        'integrante_id',
        'inicio_cargo',
        'fin_cargo',
        'periodo_habil',
        'archivo_reeleccion',
        'fecha_inicio_reeleccion',
    ];

    public function integrante()
    {
        return $this->belongsTo(Integrante::class)->withDefault();
    }

    public function consejo()
    {
        return $this->belongsTo(Consejo::class);
    }
}
