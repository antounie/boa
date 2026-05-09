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
            'destino.different'      => 'El destino debe ser diferente al origen.',
            'fecha.after_or_equal'   => 'La fecha debe ser hoy o posterior.',
        ]);

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        $vuelos = ProgramacionVuelo::with([
                'vuelo',
                'ruta.aeropuertoOrigen',
                'ruta.aeropuertoDestino',
                'aeronave',
            ])
            ->withCount(['asientosProgramacion as asientos_disponibles' => function ($q) {
                $q->where('estado', 'Disponible');
            }])
            ->whereHas('ruta', function ($q) use ($request) {
                $q->where('aeropuerto_origen_id', $request->origen)
                  ->where('aeropuerto_destino_id', $request->destino);
            })
            ->where('fecha_salida', $request->fecha)
            ->where('estado', 'Programado')
            ->orderBy('hora_salida')
            ->get();

        return view('welcome', compact('aeropuertos', 'vuelos'));
    }

    public function seleccionar(ProgramacionVuelo $programacion)
    {
        if (!auth()->check()) {
            $programacion->loadMissing('ruta');

            session(['vuelo_pendiente' => [
                'programacion_id' => $programacion->id,
                'origen'          => $programacion->ruta->aeropuerto_origen_id,
                'destino'         => $programacion->ruta->aeropuerto_destino_id,
                'fecha'           => $programacion->fecha_salida,
            ]]);

            return redirect()->route('login')
                ->with('info', 'Debe iniciar sesión para comprar un pasaje.');
        }

        return redirect()->route('cliente.seleccionar.asiento', $programacion);
    }
}
