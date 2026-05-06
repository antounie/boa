<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Vuelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VueloController extends Controller
{
    public function index(Request $request)
    {
        $query = Vuelo::withCount('escalas')
            ->whereNull('vuelo_padre_id'); // Solo vuelos principales, no tramos de escala

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_vuelo', 'like', "%{$buscar}%")
                  ->orWhere('tipo', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $vuelos = $query->orderBy('id', 'desc')->paginate(10);
        return view('operador.vuelos.index', compact('vuelos'));
    }

    public function create()
    {
        $vuelosPadre = Vuelo::where('tipo', 'ConEscalas')
            ->where('estado', 'Activo')
            ->whereNull('vuelo_padre_id')
            ->get();
        return view('operador.vuelos.create', compact('vuelosPadre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_vuelo' => 'required|string|max:20|unique:vuelos',
            'tipo' => 'required|in:Directo,ConEscalas',
            'vuelo_padre_id' => 'nullable|exists:vuelos,id',
        ], [
            'codigo_vuelo.unique' => 'Este código de vuelo ya está registrado.',
        ]);

        Vuelo::create([
            'codigo_vuelo' => strtoupper($request->codigo_vuelo),
            'tipo' => $request->tipo,
            'estado' => 'Activo',
            'vuelo_padre_id' => $request->vuelo_padre_id,
        ]);

        return redirect()->route('operador.vuelos.index')
            ->with('success', 'Vuelo registrado exitosamente.');
    }

    public function show(Vuelo $vuelo)
    {
        $vuelo->load(['escalas', 'programaciones.ruta.aeropuertoOrigen', 'programaciones.ruta.aeropuertoDestino']);
        return view('operador.vuelos.show', compact('vuelo'));
    }

    public function edit(Vuelo $vuelo)
    {
        return view('operador.vuelos.edit', compact('vuelo'));
    }

    public function update(Request $request, Vuelo $vuelo)
    {
        $request->validate([
            'codigo_vuelo' => ['required', 'string', 'max:20', Rule::unique('vuelos')->ignore($vuelo->id)],
            'tipo' => 'required|in:Directo,ConEscalas',
        ], [
            'codigo_vuelo.unique' => 'Este código de vuelo ya está registrado.',
        ]);

        $vuelo->update([
            'codigo_vuelo' => strtoupper($request->codigo_vuelo),
            'tipo' => $request->tipo,
        ]);

        return redirect()->route('operador.vuelos.index')
            ->with('success', 'Vuelo actualizado exitosamente.');
    }

    public function toggleStatus(Vuelo $vuelo)
    {
        if ($vuelo->estado === 'Activo') {
            // Verificar programaciones activas
            if ($vuelo->programaciones()->where('estado', 'Programado')->count() > 0) {
                return redirect()->route('operador.vuelos.index')
                    ->with('error', "No se puede cancelar el vuelo '{$vuelo->codigo_vuelo}' porque tiene programaciones activas.");
            }

            // Cancelar vuelo y sus escalas
            $vuelo->update(['estado' => 'Cancelado']);
            $vuelo->escalas()->update(['estado' => 'Cancelado']);
            $mensaje = "Vuelo {$vuelo->codigo_vuelo} y sus escalas cancelados exitosamente.";
        } else {
            $vuelo->update(['estado' => 'Activo']);
            $vuelo->escalas()->update(['estado' => 'Activo']);
            $mensaje = "Vuelo {$vuelo->codigo_vuelo} y sus escalas reactivados exitosamente.";
        }

        return redirect()->route('operador.vuelos.index')
            ->with('success', $mensaje);
    }
}