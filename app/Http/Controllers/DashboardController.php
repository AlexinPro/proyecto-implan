<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\Integrante;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {

         //1) Totales por consejo (ya existentes)
        $consejos = Consejo::withCount('integrantes')->get();
        $labels = $consejos->pluck('nombre');              // nombres de los consejos
        $data   = $consejos->pluck('integrantes_count');   // totales por consejo


        //2) Integrantes por género y consejo (barras agrupadas)
        //se mantiene la lógica previa (clasifica textos libres como "Prefiero autodescribirme")
        $generoLabels = [
            'Mujer',
            'Hombre',
            'Prefiero autodescribirme',
            'Prefiero no responder',
        ];

        $generoData = [];
        foreach ($generoLabels as $label) {
            $generoData[$label] = [];
        }

        foreach ($consejos as $consejo) {
            $counts = [
                'Mujer' => 0,
                'Hombre' => 0,
                'Prefiero autodescribirme' => 0,
                'Prefiero no responder' => 0,
            ];

            foreach ($consejo->integrantes as $integrante) {
                $g = trim((string) $integrante->genero);

                // Normalizar a minúsculas para comparar
                $gl = mb_strtolower($g);

                if ($gl === 'mujer') {
                    $counts['Mujer']++;
                } elseif ($gl === 'hombre') {
                    $counts['Hombre']++;
                } elseif ($gl === 'prefiero no responder') {
                    $counts['Prefiero no responder']++;
                } elseif ($g === '' || $g === null) {
                    // Si quieres tratar vacíos explícitamente como "Prefiero no responder",
                    // cambia esto a $counts['Prefiero no responder']++;
                    // por ahora lo consideramos autodescrito si no coincide con los tres anteriores.
                    $counts['Prefiero autodescribirme']++;
                } else {
                    // Todo texto libre (ej. "No binario", "Agénero", etc.) -> autodescrito
                    $counts['Prefiero autodescribirme']++;
                }
            }

            foreach ($generoLabels as $label) {
                $generoData[$label][] = $counts[$label];
            }
        }

         //3) TOTALES GLOBALES POR GÉNERO (para la gráfica circular)
        // Normalizar valores de referencia a minúsculas
        $mujerCount = Integrante::whereRaw("LOWER(TRIM(genero)) = ?", ['mujer'])->count();
        $hombreCount = Integrante::whereRaw("LOWER(TRIM(genero)) = ?", ['hombre'])->count();
        $noResponderCount = Integrante::whereRaw("LOWER(TRIM(genero)) = ?", ['prefiero no responder'])->count();

        // Autodescrito = todos los registros con genero no nulo/empty y que NO estén en los 3 anteriores
        // Usamos whereRaw con NOT IN en la forma segura:
        $autodescritoCount = Integrante::whereNotNull('genero')
            ->whereRaw("TRIM(genero) <> ''")
            ->whereRaw("LOWER(TRIM(genero)) NOT IN ('mujer','hombre','prefiero no responder')")
            ->count();

        $generoTotales = [
            'Mujer' => $mujerCount,
            'Hombre' => $hombreCount,
            'Prefiero autodescribirme' => $autodescritoCount,
            'Prefiero no responder' => $noResponderCount,
        ];

        /*
         * 4) Enviar todo a la vista
         */
        return Inertia::render('Dashboard', [
            'labels'        => $labels,
            'data'          => $data,
            'generoLabels'  => $generoLabels,
            'generoData'    => $generoData,
            'generoTotales' => $generoTotales,
        ]);
    }
}
