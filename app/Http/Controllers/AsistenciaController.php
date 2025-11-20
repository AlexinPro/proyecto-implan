<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\Integrante;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index(Consejo $consejo)
    {
        // Traer los integrantes del consejo
        $integrantes = Integrante::where('consejo_id', $consejo->id)->get();

        // Traer las asistencias de esos integrantes
        $asistencias = Asistencia::whereIn('integrante_id', $integrantes->pluck('id'))->get();

        return Inertia::render('Asistencia/Index', [
            'consejo' => $consejo,
            'integrantes' => $integrantes,
            'asistencias' => $asistencias,
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $validated = $request->validate([
            'integrante_id' => 'required|exists:integrantes,id',
            'tipo_sesion' => 'required|in:ordinaria,solemne,extraordinaria',
            'fecha' => 'required|date',
            'asistio' => 'required|boolean',
        ]);

        // Genera el mes automáticamente
        $validated['mes'] = date('F', strtotime($validated['fecha']));

        Asistencia::create($validated);

        return back()->with('success', 'Asistencia registrada correctamente');
    }
}
