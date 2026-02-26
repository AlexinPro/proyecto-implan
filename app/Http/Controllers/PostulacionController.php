<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\Consejo;
use App\Models\Integrante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostulacionController extends Controller
{
    /**
     * Muestra el listado general de postulaciones.
     */
    public function index()
    {
        $postulaciones = Postulacion::with('consejo')
            ->latest()
            ->get();

        $consejos = Consejo::orderBy('nombre')->get();

        return Inertia::render('Postulaciones/Index', [
            'postulaciones' => $postulaciones,
            'consejos' => $consejos,
        ]);
    }

    /**
 * Almacena una nueva postulación con sus 8 documentos obligatorios.
 */
public function store(Request $request)
{
    // Validación base
    $rules = [
        'nombre'      => 'required|string|max:255',
        'apellidos'   => 'required|string|max:255',
        'consejo_id'  => 'required|exists:consejos,id',
        'puesto'      => 'required|string|max:255',
    ];

    // Agregar validación dinámica para los 8 documentos obligatorios
    foreach (Postulacion::TIPOS_DOCUMENTOS as $tipo) {
        $rules["documentos.$tipo"] = 'required|file|mimes:pdf|max:2048';
    }

    $validated = $request->validate($rules);

    return DB::transaction(function () use ($request) {

        // 1. Crear la postulación
        $postulacion = Postulacion::create([
            'nombre'     => $request->nombre,
            'apellidos'  => $request->apellidos,
            'consejo_id' => $request->consejo_id,
            'puesto'     => $request->puesto,
            'estatus'    => 'pendiente',
            'postulacion' => now(),
        ]);

        // 2. Guardar los documentos obligatorios
        foreach (Postulacion::TIPOS_DOCUMENTOS as $tipo) {

            $archivo = $request->file("documentos.$tipo");

            $ruta = $archivo->store('postulaciones', 'public');

            PostulacionDocumento::create([
                'postulacion_id' => $postulacion->id,
                'tipo'           => $tipo,
                'archivo'        => $ruta,
            ]);
        }

        return redirect()
            ->route('postulaciones.index')
            ->with('success', 'Postulación creada correctamente con expediente completo.');
    });
}

    /**
     * Muestra únicamente las postulaciones pendientes de validación.
     */
    public function validacion()
    {
        $postulaciones = Postulacion::with('consejo')
            ->where('estatus', 'pendiente')
            ->latest()
            ->get();

        return Inertia::render('Postulaciones/Postulacion', [
            'postulaciones' => $postulaciones,
        ]);
    }

    /**
     * Aprueba una postulación.
     * 
     * - Verifica que esté pendiente.
     * - Evita duplicados en integrantes.
     * - Crea el integrante automáticamente.
     * - Actualiza la postulación.
     * - Todo dentro de una transacción.
     */
public function aprobar(Request $request, Postulacion $postulacion)
{
    $request->validate([
        'fecha_validacion' => 'required|date',
    ]);

    return DB::transaction(function () use ($postulacion, $request) {

        if ($postulacion->estatus !== 'pendiente') {
            return redirect()
                ->route('postulaciones.validacion')
                ->with('error', 'Esta postulación ya fue procesada.');
        }

        $existe = Integrante::where('nombre', $postulacion->nombre)
            ->where('apellido', $postulacion->apellidos)
            ->where('consejo_id', $postulacion->consejo_id)
            ->exists();

        if ($existe) {
            return redirect()
                ->route('postulaciones.validacion')
                ->with('error', 'El integrante ya fue agregado previamente.');
        }

        Integrante::create([
            'nombre'            => $postulacion->nombre,
            'apellido'          => $postulacion->apellidos,
            'puesto'            => $postulacion->puesto,
            'correo'            => null,
            'genero'            => null,
            'colonia'           => null,
            'discapacidad'      => null,
            'discapacidad_tipo' => null,
            'consejo_id'        => $postulacion->consejo_id,
        ]);

        $postulacion->update([
            'estatus'          => 'aprobada',
            'validado_por'     => Auth::id(),
            'fecha_validacion' => $request->fecha_validacion,
        ]);

        return redirect()
            ->route('postulaciones.validacion')
            ->with('success', 'Postulación aprobada correctamente.');
    });
}
    /**
     * Rechaza una postulación.
     */
public function rechazar(Request $request, Postulacion $postulacion)
{
    $request->validate([
        'fecha_validacion' => 'required|date',
    ]);

    if ($postulacion->estatus !== 'pendiente') {
        return redirect()
            ->route('postulaciones.validacion')
            ->with('error', 'Esta postulación ya fue procesada.');
    }

    $postulacion->update([
        'estatus'          => 'no_aprobada',
        'validado_por'     => Auth::id(),
        'fecha_validacion' => $request->fecha_validacion,
    ]);

    return redirect()
        ->route('postulaciones.validacion')
        ->with('success', 'Postulación rechazada correctamente.');
}
}