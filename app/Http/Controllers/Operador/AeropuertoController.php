<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Aeropuerto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AeropuertoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aeropuerto::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_IATA', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('ciudad', 'like', "%{$buscar}%")
                  ->orWhere('pais', 'like', "%{$buscar}%");
            });
        }

        $aeropuertos = $query->orderBy('codigo_IATA', 'asc')->paginate(10);
        return view('operador.aeropuertos.index', compact('aeropuertos'));
    }

    public function create()
    {
        return view('operador.aeropuertos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_IATA' => 'required|string|size:3|alpha|unique:aeropuertos',
            'nombre' => 'required|string|max:150',
            'ciudad' => 'required|string|max:80',
            'pais' => 'required|string|max:80',
        ], [
            'codigo_IATA.size' => 'El código IATA debe tener exactamente 3 letras.',
            'codigo_IATA.alpha' => 'El código IATA solo debe contener letras.',
            'codigo_IATA.unique' => 'Este código IATA ya está registrado.',
        ]);

        Aeropuerto::create([
            'codigo_IATA' => strtoupper($request->codigo_IATA),
            'nombre' => $request->nombre,
            'ciudad' => $request->ciudad,
            'pais' => $request->pais,
        ]);

        return redirect()->route('operador.aeropuertos.index')
            ->with('success', 'Aeropuerto registrado exitosamente.');
    }

    public function edit(Aeropuerto $aeropuerto)
    {
        return view('operador.aeropuertos.edit', compact('aeropuerto'));
    }

    public function update(Request $request, Aeropuerto $aeropuerto)
    {
        $request->validate([
            'codigo_IATA' => ['required', 'string', 'size:3', 'alpha', Rule::unique('aeropuertos')->ignore($aeropuerto->id)],
            'nombre' => 'required|string|max:150',
            'ciudad' => 'required|string|max:80',
            'pais' => 'required|string|max:80',
        ], [
            'codigo_IATA.size' => 'El código IATA debe tener exactamente 3 letras.',
            'codigo_IATA.alpha' => 'El código IATA solo debe contener letras.',
            'codigo_IATA.unique' => 'Este código IATA ya está registrado.',
        ]);

        $aeropuerto->update([
            'codigo_IATA' => strtoupper($request->codigo_IATA),
            'nombre' => $request->nombre,
            'ciudad' => $request->ciudad,
            'pais' => $request->pais,
        ]);

        return redirect()->route('operador.aeropuertos.index')
            ->with('success', 'Aeropuerto actualizado exitosamente.');
    }

    public function destroy(Aeropuerto $aeropuerto)
    {
        // Verificar dependencias con rutas
        if ($aeropuerto->rutasOrigen()->count() > 0 || $aeropuerto->rutasDestino()->count() > 0) {
            return redirect()->route('operador.aeropuertos.index')
                ->with('error', "No se puede eliminar el aeropuerto '{$aeropuerto->codigo_IATA}' porque tiene rutas asociadas.");
        }

        $aeropuerto->delete();

        return redirect()->route('operador.aeropuertos.index')
            ->with('success', 'Aeropuerto eliminado exitosamente.');
    }
}