<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\Integrante;
use App\Models\Legalidad;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LegalidadController extends Controller
{
    public function index(Consejo $consejo)
    {
        return Inertia::render('Legalidad/Index', [
            'consejo' => $consejo,
            'integrantes' => Integrante::where('consejo_id', $consejo->id)->get(),
            'registros' => Legalidad::with('integrante')
                ->where('consejo_id', $consejo->id)
                ->get(),
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $validated = $request->validate([
        'integrante_id' => 'required|exists:integrantes,id',
        'inicio_cargo'  => 'required|date',
        'fin_cargo'     => 'required|date|after_or_equal:inicio_cargo',
        'archivo'       => 'nullable|file|mimes:pdf|max:5120',
    ]);

    $inicio = Carbon::parse($validated['inicio_cargo']);
    $fin    = Carbon::parse($validated['fin_cargo']);

    $periodo = $inicio->diff($fin);
    $periodoHabil = sprintf(
        '%02d:%02d:%02d',
        $periodo->y,
        $periodo->m,
        $periodo->d
    );

    /* =========================
       MANEJO DE ARCHIVO (OPCIONAL)
    ========================= */
    $rutaArchivo = null;
    $fechaInicioReeleccion = null;

    if ($request->hasFile('archivo')) {
        $rutaArchivo = $request->file('archivo')
            ->store('legalidad/reelecciones', 'public');

        $fechaInicioReeleccion = now();
    }

    Legalidad::create([
        'consejo_id'               => $consejo->id,
        'integrante_id'            => $validated['integrante_id'],
        'inicio_cargo'             => $validated['inicio_cargo'],
        'fin_cargo'                => $validated['fin_cargo'],
        'periodo_habil'            => $periodoHabil,
        'archivo_reeleccion'       => $rutaArchivo,
        'fecha_inicio_reeleccion'  => $fechaInicioReeleccion,
    ]);

    return back()->with('success', 'Periodo registrado correctamente.');
    }

    public function destroy(Legalidad $legalidad)
    {
        if ($legalidad->archivo_reeleccion) {
            Storage::disk('public')->delete($legalidad->archivo_reeleccion);
        }

        $legalidad->delete();

        return back()->with('success', 'Registro eliminado.');
    }
}
