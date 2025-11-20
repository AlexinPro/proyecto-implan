<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;
use App\Models\Integrante;
use App\Models\Asistencia;

class ReporteController extends Controller
{
    public function index(Consejo $consejo)
    {
        $convocatorias = Convocatoria::where('consejo_id', $consejo->id)->get();

        $asistencias = Asistencia::whereHas('integrante', function ($q) use ($consejo) {
            $q->where('consejo_id', $consejo->id);
        })
            ->with(['integrante:id,nombre,apellido'])
            ->get();

        $integrantes = $consejo->integrantes()
            ->with('documentos:id,integrante_id,tipo,archivo')
            ->get(['id', 'nombre', 'apellido']);

        $hayDatos = $convocatorias->isNotEmpty() || $asistencias->isNotEmpty() || $integrantes->isNotEmpty();

        return Inertia::render('Reportes/Index', [
            'consejo' => $consejo,
            'convocatorias' => $convocatorias,
            'asistencias' => $asistencias,
            'integrantes' => $integrantes,
            'hayDatos' => $hayDatos,
        ]);
    }
}
