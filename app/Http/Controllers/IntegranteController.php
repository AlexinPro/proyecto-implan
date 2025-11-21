<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integrante;
use App\Models\Consejo;
use Inertia\Inertia;

class IntegranteController extends Controller
{
    public function index(Consejo $consejo){
        $integrantes = $consejo->integrantes()->get();
        return Inertia::render('Integrantes/Index', [
            'consejo' => $consejo, 
            'integrantes' => $integrantes
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'genero' => 'required|string|max:50',
            'colonia' => 'nullable|string|max:255',
            'discapacidad' => 'required|string|max:25',
            'discapacidad_tipo' => 'nullable|string|max:255|required_if:discapacidad,si',
            'puesto' => 'required|string|max:255',
            'correo' => 'required|email|unique:integrantes,correo',
            'consejo_id' => 'required|exists:consejos,id',
        ]);

        Integrante::create($validated);

        return redirect()->route('consejos.integrantes', $request->consejo_id);
    }

    public function update(Request $request, Integrante $integrante){
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'genero' => 'required|string|max:50',
            'colonia' => 'nullable|string|max:255',
            'discapacidad' => 'required|string|max:25',
            'discapacidad_tipo' => 'nullable|string|max:255|required_if:discapacidad,si',
            'puesto' => 'required|string|max:255',
            'correo' => 'required|email|unique:integrantes,correo,' . $integrante->id,
        ]);

        $integrante->update($validated);

        return redirect()->route('consejos.integrantes', $integrante->consejo_id);
    }

    public function destroy(Integrante $integrante){
        $consejoId = $integrante->consejo_id;
        $integrante->delete();

        return redirect()->route('consejos.integrantes', $consejoId);
    }
}
