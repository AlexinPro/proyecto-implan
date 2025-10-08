<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integrante;
use App\Models\Consejo;
use Inertia\Inertia;
use Ramsey\Uuid\Type\Integer;

class IntegranteController extends Controller
{
    public function index(Consejo $consejo){
        $integrantes = $consejo->integrantes()->get();//obtener integrantes
        return Inertia::render('Integrantes/Index', 
        ['consejo' => $consejo, 'integrantes' => $integrantes]);
    }
        //funcion para crear
    public function store(Request $request){
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'puesto' => 'required|string|max:255',
            'correo' => 'required|email|unique:integrantes,correo',
            'consejo_id' => 'required|exists:consejos,id',
        ]);

        Integrante::create($validated);
        return redirect()->route('consejos.integrantes', $request->consejo_id);
    }
    //funcion para editar
    public function update(Request $request, Integrante $integrante){
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'puesto' => 'required|string|max:255',
        'correo' => 'required|email|unique:integrantes,correo,' . $integrante->id,
    ]);

    $integrante->update($validated);
    return redirect()->route('consejos.integrantes', $integrante->consejo_id);
    }
    //funcion para eliminar
    public function destroy(Integrante $integrante){
        $consejoId = $integrante->consejo_id;
        $integrante->delete();
        return redirect()->route('consejos.integrantes', $consejoId);
    }    
}
