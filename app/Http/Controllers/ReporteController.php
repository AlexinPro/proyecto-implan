<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;
use App\Models\Asistencia;

class ReporteController extends Controller
{
    public function index(Consejo $consejo)
    {
        // Traer convocatorias del consejo seleccionado
        $convocatorias = Convocatoria::where('consejo_id', $consejo->id)->get();

        // Traer asistencias que pertenezcan a convocatorias de este consejo
        $asistencias = Asistencia::whereHas('convocatoria', function ($q) use ($consejo) {
            $q->where('consejo_id', $consejo->id);
        })->with(['convocatoria', 'integrante'])->get();

        $hayDatos = $convocatorias->isNotEmpty() || $asistencias->isNotEmpty();

        return Inertia::render('Reportes/Index', [
            'consejo' => $consejo,
            'convocatorias' => $convocatorias,
            'asistencias' => $asistencias,
            'hayDatos' => $hayDatos,
        ]);
    }
}
