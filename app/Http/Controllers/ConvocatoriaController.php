<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;

class ConvocatoriaController extends Controller
{
    public function index(Consejo $consejo)
    {
        $convocatorias = $consejo->convocatorias()
            ->orderBy('fecha', 'asc')
            ->get();

        return Inertia::render('Convocatoria/Index', [
            'consejo' => $consejo,
            'convocatorias' => $convocatorias,
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $data = $request->validate([
            'tipo_sesion' => 'required|in:ordinaria,solemne,extraordinaria',
            'fecha' => 'required|date',
            'documento' => 'nullable|file|mimes:pdf|max:4096', // Máximo 4MB
            'estado_convocatoria' => 'boolean',
            'estado_sesion' => 'boolean',
        ]);

        if ($request->hasFile('documento')) {
            $data['documento'] = $request->file('documento')->store('convocatorias', 'public');
        }

        $data['consejo_id'] = $consejo->id;

        Convocatoria::create($data);

        return back()->with('success', 'Convocatoria guardada correctamente.');
    }
}
