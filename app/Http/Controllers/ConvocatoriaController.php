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
    //Validación base
    $data = $request->validate([
        'tipo_sesion' => 'required|in:ordinaria,solemne,extraordinaria',
        'fecha' => 'required|date',
        'documento' => 'nullable|file|mimes:pdf|max:4096',
        'estado_convocatoria' => 'nullable|boolean',
        'estado_sesion' => 'nullable|boolean',
    ]);

    //Normalizar booleanos (checkbox no marcado = false)
    $data['estado_convocatoria'] = $data['estado_convocatoria'] ?? false;
    $data['estado_sesion'] = $data['estado_sesion'] ?? false;

    //Regla de negocio: no sesión sin convocatoria
    if ($data['estado_sesion'] && !$data['estado_convocatoria']) {
        return back()->withErrors([
            'estado_sesion' => 'No se puede registrar una sesión sin convocatoria.',
        ]);
    }

    //Guardar documento solo si corresponde
    if ($request->hasFile('documento')) {
        $data['documento'] = $request->file('documento')->store('convocatorias', 'public');
    }

    //Relación con consejo
    $data['consejo_id'] = $consejo->id;

    //Crear convocatoria
    Convocatoria::create($data);

    return back()->with('success', 'Convocatoria guardada correctamente.');
}

}
