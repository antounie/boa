<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Empleado::withCount('tripulaciones');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('licencia', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('cargo')) {
            $query->where('cargo', $request->cargo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $empleados = $query->orderBy('apellido', 'asc')->paginate(10);
        return view('operador.empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('operador.empleados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'cargo' => 'required|in:Piloto,Copiloto,Auxiliar',
            'licencia' => 'nullable|string|max:50',
        ]);

        Empleado::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cargo' => $request->cargo,
            'licencia' => $request->licencia,
            'estado' => 'Activo',
        ]);

        return redirect()->route('operador.empleados.index')
            ->with('success', 'Empleado registrado exitosamente.');
    }

    public function edit(Empleado $empleado)
    {
        return view('operador.empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'cargo' => 'required|in:Piloto,Copiloto,Auxiliar',
            'licencia' => 'nullable|string|max:50',
        ]);

        $empleado->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cargo' => $request->cargo,
            'licencia' => $request->licencia,
        ]);

        return redirect()->route('operador.empleados.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    public function toggleStatus(Empleado $empleado)
    {
        if ($empleado->estado === 'Activo') {
            $asignaciones = $empleado->tripulaciones()
                ->whereHas('programacionVuelo', function ($q) {
                    $q->where('estado', 'Programado');
                })->count();

            if ($asignaciones > 0) {
                return redirect()->route('operador.empleados.index')
                    ->with('error', "No se puede desactivar a {$empleado->nombre} {$empleado->apellido} porque tiene {$asignaciones} vuelo(s) programado(s).");
            }

            $empleado->update(['estado' => 'Inactivo']);
            $mensaje = "Empleado {$empleado->nombre} {$empleado->apellido} desactivado exitosamente.";
        } else {
            $empleado->update(['estado' => 'Activo']);
            $mensaje = "Empleado {$empleado->nombre} {$empleado->apellido} reactivado exitosamente.";
        }

        return redirect()->route('operador.empleados.index')
            ->with('success', $mensaje);
    }
}