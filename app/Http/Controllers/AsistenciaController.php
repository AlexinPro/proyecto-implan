<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index(Consejo $consejo)
    {
        $integrantes = $consejo->integrantes;

        // Tomamos el mes actual para ejemplo, puedes cambiar esto
        $inicioMes = Carbon::now()->startOfMonth();
        $diasMes = $inicioMes->daysInMonth;

        // Crear colección de fechas del mes
        $fechas = collect();
        for ($d = 1; $d <= $diasMes; $d++) {
            $fechas->push($inicioMes->copy()->day($d)->toDateString());
        }

        // Aquí deberías traer las asistencias reales desde la DB (simulación: todos presentes)
        $asistencia = [];
        foreach ($integrantes as $integrante) {
            foreach ($fechas as $fecha) {
                $asistencia[$integrante->id][$fecha] = true; // true = asistió, false = no asistió
            }
        }

        return Inertia::render('Asistencias/Index', [
            'consejo' => $consejo,
            'integrantes' => $integrantes,
            'fechas' => $fechas,
            'asistencia' => $asistencia,
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $data = $request->validate([
            'asistencia' => 'required|array',
            'asistencia.*.*' => 'boolean',
        ]);

        // Aquí debes guardar la asistencia en tu base de datos
        // Ejemplo: recorrer y guardar cada registro

        foreach ($data['asistencia'] as $integranteId => $fechas) {
            foreach ($fechas as $fecha => $asistio) {
                // Guardar en DB según tu modelo y lógica
                // Ejemplo (pseudocódigo):
                // Asistencia::updateOrCreate(
                //     ['integrante_id' => $integranteId, 'fecha' => $fecha],
                //     ['asistio' => $asistio]
                // );
            }
        }

        return redirect()->route('consejos.asistencia', $consejo->id)
            ->with('success', 'Asistencia guardada correctamente.');
    }
}
