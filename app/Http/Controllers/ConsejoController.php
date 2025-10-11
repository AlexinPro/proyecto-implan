<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consejo;
use Inertia\Inertia;

class ConsejoController extends Controller
{
    public function index(Request $request)
    {
        //detectar la ruta de la que llego el usuario: Archivo digital o Convocatorias 
        $origen = $request->routeIs('consejos.convocatorias') ? 'convocatorias' : 'consejos';
        // ? incluye un if simplificado si la ruta es consejos.convocatorias, 
        //entonces origen es convocatorias, si no es consejos

        $consejos = Consejo::all();//recupera todos los consejos de la base de datos
        
        return Inertia::render('Consejos/Index', 
        ['consejos' => $consejos,
         'origen' => $origen]);
    }
}
