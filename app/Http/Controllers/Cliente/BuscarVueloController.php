<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Aeropuerto;
use App\Models\Tramo;
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
            'origen'  => 'required|exists:aeropuertos,id',
            'destino' => 'required|exists:aeropuertos,id|different:origen',
            'fecha'   => 'required|date|after_or_equal:today',
        ], [
            'destino.different'     => 'El destino debe ser diferente al origen.',
            'fecha.after_or_equal'  => 'La fecha debe ser hoy o posterior.',
        ]);

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        $eagerLoad = [
            'aeropuertoOrigen',
            'aeropuertoDestino',
            'aeronave',
            'rutaTramo.tramo.subTramos.aeropuertoOrigen',
            'rutaTramo.tramo.subTramos.aeropuertoDestino',
            'precios.tipoClase',
        ];

        // Vuelos de ruta completa: origen y destino coinciden directamente
        $vuelos = ProgramacionVuelo::with($eagerLoad)
            ->withCount(['asientosProgramacion as asientos_disponibles' => function ($q) {
                $q->where('estado', 'Disponible');
            }])
            ->where('aeropuerto_origen_id', $request->origen)
            ->where('aeropuerto_destino_id', $request->destino)
            ->where('fecha_salida', $request->fecha)
            ->where('estado', 'Programado')
            ->orderBy('hora_salida')
            ->get();

        // Vuelos con escala: el origen/destino coincide con un sub-tramo
        $vuelosConSubTramo = ProgramacionVuelo::with($eagerLoad)
            ->withCount(['asientosProgramacion as asientos_disponibles' => function ($q) {
                $q->where('estado', 'Disponible');
            }])
            ->whereHas('rutaTramo.tramo.subTramos', function ($q) use ($request) {
                $q->where('aeropuerto_origen_id', $request->origen)
                  ->where('aeropuerto_destino_id', $request->destino);
            })
            ->where('fecha_salida', $request->fecha)
            ->where('estado', 'Programado')
            ->orderBy('hora_salida')
            ->get();

        // Para cada vuelo con sub-tramo, identificar el sub-tramo específico que coincide
        $resultadosParciales = collect();
        foreach ($vuelosConSubTramo as $prog) {
            $subTramo = $prog->rutaTramo?->tramo?->subTramos
                ->where('aeropuerto_origen_id', (int) $request->origen)
                ->where('aeropuerto_destino_id', (int) $request->destino)
                ->first();

            if ($subTramo) {
                $resultadosParciales->push([
                    'programacion' => $prog,
                    'sub_tramo'    => $subTramo,
                ]);
            }
        }

        return view('cliente.buscar', compact('aeropuertos', 'vuelos', 'resultadosParciales'));
    }

    public function seleccionarAsiento(ProgramacionVuelo $programacion, Request $request)
    {
        session()->forget('vuelo_pendiente');

        $programacion->load([
            'aeropuertoOrigen',
            'aeropuertoDestino',
            'aeronave',
            'rutaTramo.tramo.subTramos.aeropuertoOrigen',
            'rutaTramo.tramo.subTramos.aeropuertoDestino',
            'precios.tipoClase',
        ]);

        // Si viene con sub_tramo_id, validar que pertenezca a este vuelo
        $subTramo = null;
        if ($request->filled('sub_tramo_id')) {
            $subTramo = $programacion->rutaTramo?->tramo?->subTramos
                ->firstWhere('id', (int) $request->sub_tramo_id);
        }

        $asientos = AsientoProgramacion::with(['asiento.tipoClase'])
            ->where('programacion_vuelo_id', $programacion->id)
            ->get()
            ->sortBy(function ($ap) {
                return [$ap->asiento->fila, $ap->asiento->numero];
            });

        $clases = \App\Models\TipoClase::all();

        return view('cliente.seleccionar-asiento', compact('programacion', 'asientos', 'clases', 'subTramo'));
    }
}
