<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Obtener el total de integrantes por consejo
        $consejos = Consejo::withCount('integrantes')->get();

        // Extraer nombres y totales para Chart.js
        $labels = $consejos->pluck('nombre'); // o el campo que uses
        $data = $consejos->pluck('integrantes_count');

        return Inertia::render('Dashboard', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
