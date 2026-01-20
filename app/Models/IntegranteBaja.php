<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegranteBaja extends Model
{
    use HasFactory;

    protected $table = 'integrante_bajas';
    
    protected $fillable = [
        'integrante_id',
        'consejo_id',
        'nombre',
        'apellido',
        'motivo',
        'fecha_baja',
        'evidencia_pdf',
    ];  

    protected $casts = [
        'fecha_baja' => 'date',
    ];

    // Relaciones
    public function integrante(){
        return $this->belongsTo(Integrante::class);
    }
    public function consejo(){
        return $this->belongsTo(Consejo::class);
    }
}
