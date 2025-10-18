<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Consejo;
use App\Models\Convocatoria;
use Illuminate\Support\Facades\Storage;

class ConvocatoriaController extends Controller
{
    public function index(Consejo $consejo)
    {
        $convocatorias = Convocatoria::where('consejo_id', $consejo->id)->get();

        return Inertia::render('Convocatoria/Index', [
            'consejo' => $consejo,
            'convocatorias' => $convocatorias,
        ]);
    }

    public function store(Request $request, Consejo $consejo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'documento' => 'required|file|mimes:pdf|max:2048',
            'estado_convocatoria' => 'boolean',
            'estado_sesion' => 'boolean',
        ]);

        if ($request->hasFile('documento')) {
            $data['documento'] = $request->file('documento')->store('convocatorias', 'public');
        }

        $data['consejo_id'] = $consejo->id;

        Convocatoria::create($data);

        return redirect()->back()->with('success', 'Convocatoria guardada correctamente.');
    }
}
