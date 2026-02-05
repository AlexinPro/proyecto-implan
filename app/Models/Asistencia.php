<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'integrante_id',
        'mes',
        'tipo_sesion',
        //'asistio',
        'estado',
        'evidencia',
        'fecha',
    ];

    //Relación: una asistencia pertenece a un integrante
    public function integrante()
    {
        return $this->belongsTo(Integrante::class);
    }

    //Relación: una asistencia pertenece a una convocatoria
    public function convocatoria()
    {
        return $this->belongsTo(Convocatoria::class);
    }       
}
