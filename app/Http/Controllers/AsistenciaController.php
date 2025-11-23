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

    public function calendar(Consejo $consejo)
    {
        return Inertia::render('Asistencia/Calendar', [
            'consejo' => $consejo,
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $validated = $request->validate([
            'integrante_id' => 'required|exists:integrantes,id',
            'tipo_sesion' => 'required|in:ordinaria,solemne,extraordinaria',
            'fecha' => 'required|date',
            'asistio' => 'required|boolean',
            'evidencia' => 'nullable|file|mimes:pdf|max:4096',
        ]);

        // Genera automáticamente el mes
        $validated['mes'] = date('F', strtotime($validated['fecha']));

        // Guarda el PDF si viene en la solicitud
        if ($request->hasFile('evidencia')) {
            $validated['evidencia'] = $request->file('evidencia')->store('evidencias', 'public');
        }

        // Guarda la asistencia con la ruta del PDF (si existe)
        Asistencia::create($validated);

        return back()->with('success', 'Asistencia registrada correctamente');
    }
}
