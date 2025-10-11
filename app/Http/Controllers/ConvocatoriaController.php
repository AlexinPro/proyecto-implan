<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Consejo;

class ConvocatoriaController extends Controller
{
    public function index($consejo)
    {
        $consejo = Consejo::findOrFail($consejo);
        return Inertia::render('Convocatoria/Index', [
            'consejo' => $consejo
        ]);
    }
}