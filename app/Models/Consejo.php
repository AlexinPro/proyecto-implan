<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consejo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    //relacion con integrantes muchos a muchos
    public function integrantes(){
        return $this->hasMany(Integrante::class);
    }

    //relacion con convocatorias uno a muchos
    public function convocatorias(){
        return $this->hasMany(Convocatoria::class);
    }
}
