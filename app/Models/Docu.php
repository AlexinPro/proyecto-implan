<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Docu extends Model
{
    use HasFactory;

    protected $fillable = [
        'integrante_id',
        'nombre',
        'ruta',
    ];

    public function integrante()
    {
        return $this->belongsTo(Integrante::class);
    }
}
