<?php

namespace App\Http\Controllers;

use App\Models\Docu;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Integrante;
use Illuminate\Support\Facades\Storage;

class DocuController extends Controller
{
    public function index(Integrante $integrante)
    {
        // Tipos de documentos definidos
        $tipos = [
            'INE',
            'Comprobante domiciliario',
            'Curriculum vitae',
            'Integracion de formula',
            'Carta de motivos',
            'Cumplimiento de normatividad',
        ];

        // Traer los documentos existentes
        $documentos = $integrante->docus()->get();

        return Inertia::render('Docu/Index', [
            'integrante' => $integrante,
            'tipos' => $tipos,
            'documentos' => $documentos,
        ]);
    }

    // Guardar o reemplazar documento
    public function store(Request $request, Integrante $integrante)
    {
        $request->validate([
            'nombre' => 'required|string',
            'archivo' => 'required|file|mimes:pdf|max:4096',
        ]);

        $ruta = $request->file('archivo')->store('docus', 'public');
        /* Manejar la carga del archivo
        if ($request->hasFile('ruta')) {
            $file = $request->file('ruta');
            $path = $file->store('docus', 'public'); // Guardar en storage/app/public/docus
        }*/

        Docu::updateOrCreate(
            ['integrante_id' => $integrante->id, 'nombre' => $request->nombre],
            ['ruta' => $ruta]
        );

        return redirect()->back()->with('success', 'Documento guardado correctamente.');
    }

    // Descargar PDF
    public function download(Docu $docu)
    {
        $path = Storage::disk('public')->path($docu->ruta);
        return response()->download($path);
    }

    // Confirmar envío de documentos
    public function enviarDocumentos(Integrante $integrante)
    {
        // Aquí podrías actualizar un estado de envío en BD si quisieras
        return redirect()->back()->with('success', 'Documentos enviados correctamente.');
    }
}
