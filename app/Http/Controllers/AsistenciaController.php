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

    public function create(Consejo $consejo, Request $request)
    {
        return Inertia::render('Asistencia/Form', [
            'consejo' => $consejo,
            'fecha' => $request->fecha, // fecha seleccionada del calendario
            'integrantes' => Integrante::where('consejo_id', $consejo->id)->get(),
        ]);
    }

    //vista calendario de asistencias
    public function calendar(Consejo $consejo)
    {
        $integrantes = Integrante::where('consejo_id', $consejo->id)->get();

        // 🔥 Traer todas las sesiones del consejo (únicamente fecha y tipo)
        $sesiones = Asistencia::whereIn('integrante_id', $integrantes->pluck('id'))
            ->select('fecha', 'tipo_sesion')
            ->groupBy('fecha', 'tipo_sesion') // evitar duplicados
            ->get();

        return Inertia::render('Asistencia/Calendar', [
            'consejo' => $consejo,
            'integrantes' => $integrantes,
            'sesiones' => $sesiones,
        ]);
    }

    //vista historial de asistencias de un integrante
    public function history($consejoId, $integranteId)
    {
        // Obtener integrante y validar que pertenece al consejo
        $integrante = Integrante::where('id', $integranteId)
            ->where('consejo_id', $consejoId)
            ->firstOrFail();

        // Obtener todas las asistencias del integrante
        $historial = Asistencia::where('integrante_id', $integranteId)
            ->orderBy('fecha', 'desc')
            ->get(['id', 'fecha', 'asistio', 'tipo_sesion']);

        // Retornamos a la vista History.vue vía Inertia
        return Inertia::render('Asistencia/History', [
            'integrante' => $integrante,
            'historial' => $historial
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

        // Guarda el PDF si viene en la solicitud
        /*if ($request->hasFile('evidencia')) {
            $validated['evidencia'] = $request->file('evidencia')->store('evidencias', 'public');
        }*/

        // Guarda la asistencia con la ruta del PDF (si existe)
        Asistencia::create($validated);

        return back()->with('success', 'Asistencia registrada correctamente');
    }
}
