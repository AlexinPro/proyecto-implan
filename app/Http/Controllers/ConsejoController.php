<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consejo;
use Inertia\Inertia;

class ConsejoController extends Controller
{
    public function index()
    {
        $consejos = Consejo::all();//recupera todos los consejos de la base de datos
        
        return Inertia::render('Consejos/Index', 
        ['consejos' => $consejos]);
    }
}
