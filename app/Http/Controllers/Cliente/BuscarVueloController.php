<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Aeropuerto;
use App\Models\Asiento;
use Illuminate\Http\Request;
use App\Models\AsientoProgramacion;

class BuscarVueloController extends Controller
{
    public function index()
    {
        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();
        return view('cliente.buscar', compact('aeropuertos'));
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'origen' => 'required|exists:aeropuertos,id',
            'destino' => 'required|exists:aeropuertos,id|different:origen',
            'fecha' => 'required|date|after_or_equal:today',
        ], [
            'destino.different' => 'El destino debe ser diferente al origen.',
            'fecha.after_or_equal' => 'La fecha debe ser hoy o posterior.',
        ]);

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        $vuelos = ProgramacionVuelo::with([
                'vuelo',
                'ruta.aeropuertoOrigen',
                'ruta.aeropuertoDestino',
                'aeronave'
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

        return view('cliente.buscar', compact('aeropuertos', 'vuelos'));
    }

    public function seleccionarAsiento(ProgramacionVuelo $programacion)
    {
        session()->forget('vuelo_pendiente');

        $programacion->load(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave']);

        $asientos = AsientoProgramacion::with(['asiento.tipoClase'])
            ->where('programacion_vuelo_id', $programacion->id)
            ->get()
            ->sortBy(function ($ap) {
                return [$ap->asiento->fila, $ap->asiento->numero];
            });

        $clases = \App\Models\TipoClase::all();

        return view('cliente.seleccionar-asiento', compact('programacion', 'asientos', 'clases'));
    }
}