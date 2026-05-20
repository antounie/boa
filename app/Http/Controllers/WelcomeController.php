<?php

namespace App\Http\Controllers;

use App\Models\Aeropuerto;
use App\Models\ProgramacionVuelo;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();
        return view('welcome', compact('aeropuertos'));
    }

    public function buscarPublico(Request $request)
    {
        if ($request->isMethod('GET') && !$request->hasAny(['origen', 'destino', 'fecha'])) {
            return redirect()->route('welcome');
        }

        $request->validate([
            'origen' => 'required|exists:aeropuertos,id',
            'destino' => 'required|exists:aeropuertos,id|different:origen',
            'fecha'  => 'required|date|after_or_equal:today',
        ], [
            'destino.different'    => 'El destino debe ser diferente al origen.',
            'fecha.after_or_equal' => 'La fecha debe ser hoy o posterior.',
        ]);

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        $base = [
            'aeropuertoOrigen',
            'aeropuertoDestino',
            'aeronave',
            'rutaTramo.tramo.subTramos.aeropuertoOrigen',
            'rutaTramo.tramo.subTramos.aeropuertoDestino',
            'precios.tipoClase',
        ];

        // Vuelos de ruta completa
        $vuelos = ProgramacionVuelo::with($base)
            ->withCount(['asientosProgramacion as asientos_disponibles' => fn($q) => $q->where('estado', 'Disponible')])
            ->where('aeropuerto_origen_id', $request->origen)
            ->where('aeropuerto_destino_id', $request->destino)
            ->where('fecha_salida', $request->fecha)
            ->where('estado', 'Programado')
            ->orderBy('hora_salida')
            ->get();

        // Vuelos con sub-tramo que coincide con la ruta buscada
        $vuelosSubTramo = ProgramacionVuelo::with($base)
            ->withCount(['asientosProgramacion as asientos_disponibles' => fn($q) => $q->where('estado', 'Disponible')])
            ->whereHas('rutaTramo.tramo.subTramos', function ($q) use ($request) {
                $q->where('aeropuerto_origen_id', $request->origen)
                  ->where('aeropuerto_destino_id', $request->destino);
            })
            ->where('fecha_salida', $request->fecha)
            ->where('estado', 'Programado')
            ->orderBy('hora_salida')
            ->get();

        $resultadosParciales = collect();
        foreach ($vuelosSubTramo as $prog) {
            $subTramo = $prog->rutaTramo?->tramo?->subTramos
                ->where('aeropuerto_origen_id', (int) $request->origen)
                ->where('aeropuerto_destino_id', (int) $request->destino)
                ->first();
            if ($subTramo) {
                $resultadosParciales->push(['programacion' => $prog, 'sub_tramo' => $subTramo]);
            }
        }

        return view('welcome', compact('aeropuertos', 'vuelos', 'resultadosParciales'));
    }

    public function seleccionar(ProgramacionVuelo $programacion, \Illuminate\Http\Request $request)
    {
        $subTramoId = $request->query('sub_tramo_id');

        if (!auth()->check()) {
            session(['vuelo_pendiente' => [
                'programacion_id' => $programacion->id,
                'sub_tramo_id'    => $subTramoId,
            ]]);

            return redirect()->route('login')
                ->with('info', 'Debe iniciar sesión para comprar un pasaje.');
        }

        $params = ['programacion' => $programacion];
        if ($subTramoId) {
            $params['sub_tramo_id'] = $subTramoId;
        }

        return redirect()->route('cliente.seleccionar.asiento', $params);
    }
}
