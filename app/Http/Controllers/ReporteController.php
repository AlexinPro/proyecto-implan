<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;
use App\Models\Integrante;
use App\Models\Asistencia;
use App\Models\IntegranteBaja;

class ReporteController extends Controller
{
    public function index(Consejo $consejo)
    {
        // ✅ Convocatorias del consejo
        $convocatorias = Convocatoria::where('consejo_id', $consejo->id)->get();

        // ✅ Asistencias del consejo
        $asistencias = Asistencia::whereHas('integrante', function ($q) use ($consejo) {
            $q->where('consejo_id', $consejo->id);
        })
            ->with(['integrante:id,nombre,apellido'])
            ->get();

        // ✅ Integrantes activos
        $integrantes = $consejo->integrantes()
            ->with('documentos:id,integrante_id,tipo,archivo')
            ->get(['id', 'nombre', 'apellido']);

        // ✅ Integrantes dados de baja
        $bajas = IntegranteBaja::where('consejo_id', $consejo->id)
            ->orderBy('fecha_baja', 'desc')
            ->get([
                'id',
                'integrante_id',
                'nombre',
                'apellido',
                'motivo',
                'fecha_baja',
                'evidencia_pdf',
                'created_at',
            ]);


        // 🔹🔹🔹 NUEVO: REPORTE CONSOLIDADO DE ASISTENCIAS 🔹🔹🔹
        $reporteAsistencias = $integrantes->map(function ($integrante) use ($asistencias) {

            $asistenciasIntegrante = $asistencias->where('integrante_id', $integrante->id);

            $ordinarias = $asistenciasIntegrante
                ->where('tipo_sesion', 'ordinaria')
                ->where('asistio', true)
                ->count();

            $extraordinarias = $asistenciasIntegrante
                ->where('tipo_sesion', 'extraordinaria')
                ->where('asistio', true)
                ->count();

            $solemnes = $asistenciasIntegrante
                ->where('tipo_sesion', 'solemne')
                ->where('asistio', true)
                ->count();

            $total = $ordinarias + $extraordinarias + $solemnes;

            return [
                'integrante' => $integrante->nombre . ' ' . $integrante->apellido,
                'ordinaria' => $ordinarias,
                'extraordinaria' => $extraordinarias,
                'solemne' => $solemnes,
                'total' => $total,
            ];
        })->values();

        // 🔹🔹🔹 FIN 🔹🔹🔹


        // ✅ Validar si existe información para mostrar
        $hayDatos =
            $convocatorias->isNotEmpty() ||
            $asistencias->isNotEmpty() ||
            $integrantes->isNotEmpty() ||
            $bajas->isNotEmpty();

        return Inertia::render('Reportes/Index', [
            'consejo' => $consejo,
            'convocatorias' => $convocatorias,
            'asistencias' => $asistencias,
            'integrantes' => $integrantes,
            'bajas' => $bajas,
            'reporteAsistencias' => $reporteAsistencias, // 👈 ENVIAMOS NUEVO
            'hayDatos' => $hayDatos,
        ]);
    }
}
