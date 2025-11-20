<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use App\Models\Docu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Integrante extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nombre',
        'apellido',
        'puesto',
        'correo',
        'consejo_id',
    ];
    
    //relacion un integrane pertenece a varios consejos
    public function consejo(){
        return $this->belongsToMany(Consejo::class);
   }
   /*relación muchos a muchos con consejos a través de la tabla de apoyo
    public function consejos(){
        return $this->belongsToMany(Consejo::class, 'consejo_integrante', 'integrante_id', 'consejo_id');
    }*/
    public function documentos() 
    {
        return $this->hasMany(Docu::class,);
    }
    //relacion con asistencias: un integrante tiene muchas asistencias
    public function asistencias(){
        return $this->hasMany(Asistencia::class);
    }    
}