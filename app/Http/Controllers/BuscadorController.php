<?php

namespace App\Http\Controllers;

use App\Models\Aeropuerto;
use App\Models\ProgramacionVuelo;
use App\Models\Ruta;
use Illuminate\Http\Request;

class BuscadorController extends Controller
{
    public function buscar(Request $request)
    {
        $q = $request->input('q', '');
        $resultados = [
            'aeropuertos'   => collect(),
            'rutas'         => collect(),
            'programaciones'=> collect(),
        ];

        if (strlen($q) >= 2) {
            $resultados['aeropuertos'] = Aeropuerto::where('nombre', 'like', "%{$q}%")
                ->orWhere('ciudad', 'like', "%{$q}%")
                ->orWhere('pais', 'like', "%{$q}%")
                ->orWhere('codigo_IATA', 'like', "%{$q}%")
                ->limit(10)
                ->get();

            $resultados['rutas'] = Ruta::with(['aeropuertoOrigen', 'aeropuertoDestino'])
                ->whereHas('aeropuertoOrigen', function ($query) use ($q) {
                    $query->where('ciudad', 'like', "%{$q}%")
                          ->orWhere('codigo_IATA', 'like', "%{$q}%");
                })
                ->orWhereHas('aeropuertoDestino', function ($query) use ($q) {
                    $query->where('ciudad', 'like', "%{$q}%")
                          ->orWhere('codigo_IATA', 'like', "%{$q}%");
                })
                ->limit(10)
                ->get();

            $resultados['programaciones'] = ProgramacionVuelo::with([
                    'aeropuertoOrigen', 'aeropuertoDestino', 'aeronave'
                ])
                ->where('estado', 'Programado')
                ->where(function ($query) use ($q) {
                    $query->where('codigo_vuelo', 'like', "%{$q}%")
                        ->orWhereHas('aeropuertoOrigen', function ($q2) use ($q) {
                            $q2->where('ciudad', 'like', "%{$q}%")
                               ->orWhere('codigo_IATA', 'like', "%{$q}%");
                        })
                        ->orWhereHas('aeropuertoDestino', function ($q2) use ($q) {
                            $q2->where('ciudad', 'like', "%{$q}%")
                               ->orWhere('codigo_IATA', 'like', "%{$q}%");
                        });
                })
                ->orderBy('fecha_salida')
                ->limit(10)
                ->get();
        }

        $totalResultados = $resultados['aeropuertos']->count()
            + $resultados['rutas']->count()
            + $resultados['programaciones']->count();

        return view('buscar', compact('q', 'resultados', 'totalResultados'));
    }
}
