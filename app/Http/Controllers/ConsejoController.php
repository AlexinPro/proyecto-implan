<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consejo;
use Inertia\Inertia;

class ConsejoController extends Controller
{
    public function index()
    {
        $origen = request()->routeIs('consejos.convocatorias')
            ? 'convocatorias'
            : (request()->routeIs('consejos.asistencias')
                ? 'asistencias'
                : (request()->routeIs('consejos.reportes')
                    ? 'reportes'
                    : (request()->routeIs('consejos.legalidad')
                        ? 'legalidad'
                        : 'integrantes')));

        $consejos = Consejo::all();

        return inertia('Consejos/Index', [
            'consejos' => $consejos,
            'origen' => $origen,
        ]);
    }
}
