<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Aeronave;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AeronaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Aeronave::withCount('asientos');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('matricula', 'like', "%{$buscar}%")
                  ->orWhere('modelo', 'like', "%{$buscar}%")
                  ->orWhere('fabricante', 'like', "%{$buscar}%");
            });
        }

        $aeronaves = $query->orderBy('id', 'desc')->paginate(10);
        return view('operador.aeronaves.index', compact('aeronaves'));
    }

    public function create()
    {
        return view('operador.aeronaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:20|unique:aeronaves',
            'modelo' => 'required|string|max:80',
            'fabricante' => 'required|string|max:80',
            'capacidad_total' => 'required|integer|min:1',
        ], [
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'capacidad_total.min' => 'La capacidad debe ser al menos 1 asiento.',
        ]);

        Aeronave::create([
            'matricula' => strtoupper($request->matricula),
            'modelo' => $request->modelo,
            'fabricante' => $request->fabricante,
            'capacidad_total' => $request->capacidad_total,
            'estado' => 'Activa',
        ]);

        return redirect()->route('operador.aeronaves.index')
            ->with('success', 'Aeronave registrada exitosamente.');
    }

    public function edit(Aeronave $aeronave)
    {
        return view('operador.aeronaves.edit', compact('aeronave'));
    }

    public function update(Request $request, Aeronave $aeronave)
    {
        $request->validate([
            'matricula' => ['required', 'string', 'max:20', Rule::unique('aeronaves')->ignore($aeronave->id)],
            'modelo' => 'required|string|max:80',
            'fabricante' => 'required|string|max:80',
            'capacidad_total' => 'required|integer|min:1',
        ], [
            'matricula.unique' => 'Esta matrícula ya está registrada.',
        ]);

        $aeronave->update([
            'matricula' => strtoupper($request->matricula),
            'modelo' => $request->modelo,
            'fabricante' => $request->fabricante,
            'capacidad_total' => $request->capacidad_total,
        ]);

        return redirect()->route('operador.aeronaves.index')
            ->with('success', 'Aeronave actualizada exitosamente.');
    }

    public function toggleStatus(Aeronave $aeronave)
    {
        if ($aeronave->estado === 'Activa') {
            // Verificar programaciones activas
            if ($aeronave->programaciones()->where('estado', 'Programado')->count() > 0) {
                return redirect()->route('operador.aeronaves.index')
                    ->with('error', "No se puede dar de baja la aeronave '{$aeronave->matricula}' porque tiene programaciones activas.");
            }
            $aeronave->update(['estado' => 'Inactiva']);
            $mensaje = "Aeronave {$aeronave->matricula} dada de baja exitosamente.";
        } else {
            $aeronave->update(['estado' => 'Activa']);
            $mensaje = "Aeronave {$aeronave->matricula} reactivada exitosamente.";
        }

        return redirect()->route('operador.aeronaves.index')
            ->with('success', $mensaje);
    }
}