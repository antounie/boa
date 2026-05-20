<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Tramo;
use App\Models\Aeropuerto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RutaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruta::with(['aeropuertoOrigen', 'aeropuertoDestino']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('aeropuertoOrigen', function ($q2) use ($buscar) {
                    $q2->where('ciudad', 'like', "%{$buscar}%")
                       ->orWhere('codigo_IATA', 'like', "%{$buscar}%");
                })->orWhereHas('aeropuertoDestino', function ($q2) use ($buscar) {
                    $q2->where('ciudad', 'like', "%{$buscar}%")
                       ->orWhere('codigo_IATA', 'like', "%{$buscar}%");
                });
            });
        }

        $rutas = $query->orderBy('id', 'desc')->paginate(10);
        return view('operador.rutas.index', compact('rutas'));
    }

    public function create()
    {
        $aeropuertos = Aeropuerto::orderBy('ciudad', 'asc')->get();
        return view('operador.rutas.create', compact('aeropuertos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aeropuerto_origen_id' => 'required|exists:aeropuertos,id|different:aeropuerto_destino_id',
            'aeropuerto_destino_id' => 'required|exists:aeropuertos,id',
            'distancia' => 'required|numeric|min:1',
            'duracion_estimada' => 'required',
            'tipo' => 'required|in:Nacional,Internacional',
        ], [
            'aeropuerto_origen_id.different' => 'El aeropuerto de origen y destino deben ser diferentes.',
        ]);

        // Verificar que no exista la misma combinación origen-destino
        $existe = Ruta::where('aeropuerto_origen_id', $request->aeropuerto_origen_id)
            ->where('aeropuerto_destino_id', $request->aeropuerto_destino_id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['aeropuerto_origen_id' => 'Ya existe una ruta con este origen y destino.'])->withInput();
        }

        Ruta::create([
            'aeropuerto_origen_id' => $request->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $request->aeropuerto_destino_id,
            'distancia' => $request->distancia,
            'duracion_estimada' => $request->duracion_estimada,
            'tipo' => $request->tipo,
        ]);

        return redirect()->route('operador.rutas.index')
            ->with('success', 'Ruta registrada exitosamente.');
    }

    public function show(Ruta $ruta)
    {
        $ruta->load([
            'aeropuertoOrigen',
            'aeropuertoDestino',
            'tramos' => fn($q) => $q->with(['aeropuertoOrigen', 'aeropuertoDestino', 'subTramos.aeropuertoOrigen', 'subTramos.aeropuertoDestino']),
        ]);

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        $tramosAdjuntados = $ruta->tramos->pluck('id');
        $tramosDisponibles = Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'subTramos'])
            ->whereNull('tramo_padre_id')
            ->whereNotIn('id', $tramosAdjuntados)
            ->orderBy('id')
            ->get();

        return view('operador.rutas.show', compact('ruta', 'aeropuertos', 'tramosDisponibles'));
    }

    public function attachTramo(Request $request, Ruta $ruta)
    {
        // Adjuntar tramo existente
        if ($request->filled('tramo_id')) {
            $request->validate(['tramo_id' => 'required|exists:tramos,id', 'orden' => 'required|integer|min:1']);

            if ($ruta->tramos()->where('tramo_id', $request->tramo_id)->exists()) {
                return back()->with('error', 'Ese tramo ya está asignado a esta ruta.');
            }

            $ruta->tramos()->attach($request->tramo_id, ['orden' => $request->orden]);
            return back()->with('success', 'Tramo existente adjuntado exitosamente.');
        }

        // Crear nuevo tramo e adjuntarlo
        $request->validate([
            'aeropuerto_origen_id'  => 'required|exists:aeropuertos,id|different:aeropuerto_destino_id',
            'aeropuerto_destino_id' => 'required|exists:aeropuertos,id',
            'duracion_estimada'     => 'required',
            'orden'                 => 'required|integer|min:1',
        ], [
            'aeropuerto_origen_id.different' => 'El origen y destino deben ser diferentes.',
        ]);

        $tramo = Tramo::firstOrCreate(
            [
                'aeropuerto_origen_id'  => $request->aeropuerto_origen_id,
                'aeropuerto_destino_id' => $request->aeropuerto_destino_id,
                'tramo_padre_id'        => null,
            ],
            [
                'duracion_estimada' => $request->duracion_estimada,
                'orden'             => $request->orden,
            ]
        );

        if ($ruta->tramos()->where('tramo_id', $tramo->id)->exists()) {
            return back()->with('error', 'Ese tramo ya está asignado a esta ruta.');
        }

        $ruta->tramos()->attach($tramo->id, ['orden' => $request->orden]);
        return back()->with('success', 'Tramo agregado exitosamente.');
    }

    public function detachTramo(Ruta $ruta, Tramo $tramo)
    {
        $rutaTramo = \App\Models\RutaTramo::where('ruta_id', $ruta->id)
            ->where('tramo_id', $tramo->id)
            ->first();

        if (!$rutaTramo) {
            return back()->with('error', 'Ese tramo no pertenece a esta ruta.');
        }

        $programacionesUsando = \App\Models\ProgramacionVuelo::where('ruta_tramo_id', $rutaTramo->id)->count();
        if ($programacionesUsando > 0) {
            return back()->with('error', "No se puede quitar este tramo porque está siendo usado por {$programacionesUsando} programación(es) de vuelo. Elimina primero esas programaciones.");
        }

        $ruta->tramos()->detach($tramo->id);
        return back()->with('success', 'Tramo eliminado de la ruta.');
    }

    public function edit(Ruta $ruta)
    {
        $aeropuertos = Aeropuerto::orderBy('ciudad', 'asc')->get();
        return view('operador.rutas.edit', compact('ruta', 'aeropuertos'));
    }

    public function update(Request $request, Ruta $ruta)
    {
        $request->validate([
            'aeropuerto_origen_id' => 'required|exists:aeropuertos,id|different:aeropuerto_destino_id',
            'aeropuerto_destino_id' => 'required|exists:aeropuertos,id',
            'distancia' => 'required|numeric|min:1',
            'duracion_estimada' => 'required',
            'tipo' => 'required|in:Nacional,Internacional',
        ], [
            'aeropuerto_origen_id.different' => 'El aeropuerto de origen y destino deben ser diferentes.',
        ]);

        // Verificar duplicado excluyendo la ruta actual
        $existe = Ruta::where('aeropuerto_origen_id', $request->aeropuerto_origen_id)
            ->where('aeropuerto_destino_id', $request->aeropuerto_destino_id)
            ->where('id', '!=', $ruta->id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['aeropuerto_origen_id' => 'Ya existe una ruta con este origen y destino.'])->withInput();
        }

        $ruta->update([
            'aeropuerto_origen_id' => $request->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $request->aeropuerto_destino_id,
            'distancia' => $request->distancia,
            'duracion_estimada' => $request->duracion_estimada,
            'tipo' => $request->tipo,
        ]);

        return redirect()->route('operador.rutas.index')
            ->with('success', 'Ruta actualizada exitosamente.');
    }

    public function destroy(Ruta $ruta)
    {
        if ($ruta->programaciones()->count() > 0) {
            return redirect()->route('operador.rutas.index')
                ->with('error', "No se puede eliminar esta ruta porque tiene programaciones de vuelo asociadas.");
        }

        $ruta->delete();

        return redirect()->route('operador.rutas.index')
            ->with('success', 'Ruta eliminada exitosamente.');
    }
}