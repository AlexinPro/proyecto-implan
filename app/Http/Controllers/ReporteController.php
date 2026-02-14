<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;
use App\Models\Integrante;
use App\Models\Asistencia;
use App\Models\IntegranteBaja;

class ReporteController extends Controller
{
    /**
     * LISTA DE CONSEJOS
     * /reportes
     */
    public function index()
    {
        $consejos = Consejo::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Reportes/IndexConsejos', [
            'consejos' => $consejos,
        ]);
    }

    /**
     * REPORTES DE UN CONSEJO
     * /consejos/{consejo}/reportes
     */
    public function show(Consejo $consejo)
    {
        // ================= CONVOCATORIAS =================
        $convocatorias = Convocatoria::where('consejo_id', $consejo->id)->get();

        // ================= ASISTENCIAS (CORREGIDO) =================
        $asistencias = Asistencia::whereHas('integrante', function ($q) use ($consejo) {
                $q->where('consejo_id', $consejo->id);
            })
            ->with('integrante:id,nombre,apellido,puesto') // Carga solo los campos necesarios del integrante
            ->get([
                'id',
                'integrante_id',
                'tipo_sesion',
                'estado',
                'fecha',
                'evidencia'
            ])
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'integrante_id' => $a->integrante_id,
                    'integrante' => $a->integrante,
                    'fecha' => $a->fecha,
                    'tipo_sesion' => $a->tipo_sesion,
                    'estado' => $a->estado,

                    // 👇 Mantenemos tu simbología pero SIN perder los datos originales
                    'simbolo' => match ($a->estado) {
                        'asistio' => 'A',
                        'justificada' => 'IJ',
                        default => 'I',
                    },
                ];
            });

        // ================= INTEGRANTES =================
        $integrantes = $consejo->integrantes()
            ->with('documentos:id,integrante_id,tipo,archivo')
            ->get(['id', 'nombre', 'apellido']);

        // ================= BAJAS =================
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
            ]);

        // ================= REPORTE ASISTENCIAS
        $reporteAsistencias = $integrantes->map(function ($integrante) use ($asistencias) {

            $asistenciasIntegrante = $asistencias
                ->where('integrante_id', $integrante->id);

            $ordinarias = $asistenciasIntegrante
                ->where('tipo_sesion', 'ordinaria')
                ->where('estado', 'asistio')
                ->count();

            $extraordinarias = $asistenciasIntegrante
                ->where('tipo_sesion', 'extraordinaria')
                ->where('estado', 'asistio')
                ->count();

            $solemnes = $asistenciasIntegrante
                ->where('tipo_sesion', 'solemne')
                ->where('estado', 'asistio')
                ->count();

            return [
                'integrante' => $integrante->nombre . ' ' . $integrante->apellido,
                'ordinaria' => $ordinarias,
                'extraordinaria' => $extraordinarias,
                'solemne' => $solemnes,
                'total' => $ordinarias + $extraordinarias + $solemnes,
            ];
        })->values();

        // ================= VALIDAR DATOS =================
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
            'reporteAsistencias' => $reporteAsistencias,
            'hayDatos' => $hayDatos,
        ]);
    }
}
