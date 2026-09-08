<?php
namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Consejo;
use App\Models\Integrante;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class JustificanteController extends Controller
{
    // Vista para el integrante
    public function integrante(Consejo $consejo) {
        $integrante = Integrante::where('correo', auth()->user()->email)
            ->where('consejo_id', $consejo->id)
            ->firstOrFail();

        return Inertia::render('Asistencia/Justificantes/Integrante', [
            'consejo' => $consejo,
            'integrante' => $integrante,
        ]);
    }

    // Guardar justificante
    public function store(Request $request, Consejo $consejo) {
        // Obtener integrante autenticado perteneciente al consejo
        $integrante = Integrante::where('correo', auth()->user()->email)
            ->where('consejo_id', $consejo->id)
            ->firstOrFail();

        // Validar formulario
        $data = $request->validate([
            'fecha' => 'required|date',
            'tipo_sesion' => 'required|in:ordinaria,solemne,extraordinaria',
            'justificante' => 'required|file|mimes:pdf|max:4096',
        ]);

        // Buscar la sesión programada
        $sesion = Sesion::where('consejo_id', $consejo->id)
            ->whereDate('fecha', $data['fecha'])
            ->where('tipo_sesion', $data['tipo_sesion'])
            ->first();

        // Validar que la sesión exista
        if (!$sesion) {
            return back()->withErrors([
                'fecha' => 'La fecha seleccionada no pertenece a ninguna sesión programada.',
            ]);
        }

        // Buscar la asistencia mediante la sesión
        $asistencia = Asistencia::where('integrante_id', $integrante->id)
            ->where('sesion_id', $sesion->id)
            ->first();

        // Compatibilidad con asistencias creadas antes de usar sesion_id
        if (!$asistencia) {
            $asistencia = Asistencia::where('integrante_id', $integrante->id)
                ->whereDate('fecha', $data['fecha'])
                ->where('tipo_sesion', $data['tipo_sesion'])
                ->first();

            // Sincronizar la sesión en registros antiguos
            if ($asistencia && !$asistencia->sesion_id) {
                $asistencia->sesion_id = $sesion->id;
                $asistencia->save();
            }
        }

        // Validar que exista la asistencia
        if (!$asistencia) {
            return back()->withErrors([
                'fecha' => 'Existe la sesión, pero no se encontró un registro de asistencia para este integrante.',
            ]);
        }

        // Eliminar justificante anterior si existe
        if ($asistencia->justificante) {
            Storage::disk('public')->delete($asistencia->justificante);
        }

        // Guardar nuevo archivo PDF
        $path = $request->file('justificante')
            ->store('justificantes', 'public');

        // Actualizar justificante
        $asistencia->update([
            'justificante' => $path,
        ]);

        return back()->with(
            'success',
            'Justificante enviado correctamente.'
        );
    }

    // Vista administrativa
    public function admin(Request $request, Consejo $consejo) {
        // Integrantes disponibles para el filtro
        $integrantes = Integrante::where('consejo_id', $consejo->id)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Obtener justificantes del consejo
        $justificantes = Asistencia::with([
            'integrante',
            'sesion',
        ])
            ->whereHas('integrante', fn ($query) =>
                $query->where('consejo_id', $consejo->id))
            ->whereNotNull('justificante')
            ->when(
                $request->integrante_id,
                fn ($query, $integranteId) =>
                    $query->where('integrante_id', $integranteId)
            )
            ->orderBy('fecha', 'desc')
            ->get();

        return Inertia::render('Asistencia/Justificantes/Admin', [
            'consejo' => $consejo,
            'integrantes' => $integrantes,
            'justificantes' => $justificantes,
            'filtros' => [
                'integrante_id' => $request->integrante_id,
            ],
        ]);
    }
}