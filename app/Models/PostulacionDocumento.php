<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulacionDocumento extends Model
{
    protected $fillable = [
        'postulacion_id',
        'tipo',
        'archivo'
    ];

    public function postulacion()
    {
        return $this->belongsTo(Postulacion::class);
    }
}
