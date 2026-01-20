<?php

namespace App\Http\Controllers;

use App\Models\Integrante;
use App\Models\IntegranteBaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IntegranteBajaController extends Controller
{
    public function store(Request $request, Integrante $integrante)
    {
        // Validación de datos enviados por el formulario
        $request->validate([
            'motivo' => 'required|in:inasistencia,sancion,fin_periodo,renuncia',
            'fecha_baja' => 'required|date',
            'evidencia_pdf' => 'required|file|mimes:pdf|max:5120', // Máx 5MB
        ]);

        DB::transaction(function () use ($request, $integrante) {

            // Guardar PDF en storage/public/bajas
            $pdfPath = $request->file('evidencia_pdf')->store('bajas', 'public');

            // Registrar baja histórica
            IntegranteBaja::create([
                'integrante_id' => $integrante->id,
                'consejo_id'    => $integrante->consejo_id,
                'nombre'        => $integrante->nombre,
                'apellido'      => $integrante->apellido,
                'motivo'        => $request->motivo,
                'fecha_baja'    => $request->fecha_baja,
                'evidencia_pdf' => $pdfPath,
            ]);

            // Eliminar al integrante activo
            $integrante->delete();
        });

        return redirect()
            ->back()
            ->with('success', 'Integrante dado de baja correctamente.');
    }
}
