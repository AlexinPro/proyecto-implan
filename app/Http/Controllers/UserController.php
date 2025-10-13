<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'consejos' => Consejo::all(),
        ]);
    }

    public function asistencia(Consejo $consejo)
    {
        $integrantes = $consejo->integrantes()->get();

        // Inicio: octubre del año actual
        $inicio = Carbon::create(now()->year, 10, 1);

        $meses = [];

        for ($i = 0; $i < 12; $i++) {
            $inicioMes = $inicio->copy()->addMonths($i)->startOfMonth();
            $finMes = $inicioMes->copy()->endOfMonth();

            $fechasMes = collect();
            for ($dia = 0; $dia <= $finMes->diffInDays($inicioMes); $dia++) {
                $fechasMes->push($inicioMes->copy()->addDays($dia)->toDateString());
            }

            $meses[] = [
                'mes' => $inicioMes->format('F Y'),
                'fechas' => $fechasMes,
            ];
        }

        // Simulación: todos presentes por defecto
        $asistencia = [];
        foreach ($integrantes as $integrante) {
            $asistencia[$integrante->id] = [];
            foreach ($meses as $mes) {
                foreach ($mes['fechas'] as $fecha) {
                    $asistencia[$integrante->id][$fecha] = true;
                }
            }
        }

        return Inertia::render('Users/Asistencias', [
            'consejo' => $consejo,
            'integrantes' => $integrantes->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'cargo' => $user->puesto ?? 'Sin cargo',
                ];
            }),
            'meses' => $meses,
            'asistencia' => $asistencia,
        ]);
    }
}
