<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Tripulacion;
use App\Models\ProgramacionVuelo;
use App\Models\Empleado;
use Illuminate\Http\Request;

class TripulacionController extends Controller
{
    public function index(Request $request)
    {
        $programaciones = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino'])
            ->where('estado', 'Programado')
            ->orderBy('fecha_salida', 'asc')
            ->get();

        $programacionSeleccionada = null;
        $tripulacion = collect();

        if ($request->filled('programacion_id')) {
            $programacionSeleccionada = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'aeronave'])
                ->find($request->programacion_id);

            $tripulacion = Tripulacion::with('empleado')
                ->where('programacion_vuelo_id', $request->programacion_id)
                ->orderByRaw("FIELD(cargo, 'Piloto', 'Copiloto', 'Auxiliar')")
                ->get();
        }

        return view('operador.tripulaciones.index', compact('programaciones', 'programacionSeleccionada', 'tripulacion'));
    }

    public function create(Request $request)
    {
        $programacion = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino'])
            ->findOrFail($request->programacion_id);

        $cargo = $request->cargo ?? 'Piloto';

        // Obtener empleados activos del cargo seleccionado que no estén ya asignados a este vuelo
        $empleadosAsignados = Tripulacion::where('programacion_vuelo_id', $programacion->id)->pluck('empleado_id');

        $empleados = Empleado::where('estado', 'Activo')
            ->where('cargo', $cargo)
            ->whereNotIn('id', $empleadosAsignados)
            ->orderBy('apellido')
            ->get();

        return view('operador.tripulaciones.create', compact('programacion', 'empleados', 'cargo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'empleado_id' => 'required|exists:empleados,id',
            'cargo' => 'required|in:Piloto,Copiloto,Auxiliar',
        ]);

        // Verificar que no esté ya asignado
        $existe = Tripulacion::where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('empleado_id', $request->empleado_id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Este empleado ya está asignado a este vuelo.');
        }

        // Verificar conflicto de horario del empleado
        $programacion = ProgramacionVuelo::find($request->programacion_vuelo_id);
        $conflicto = Tripulacion::where('empleado_id', $request->empleado_id)
            ->whereHas('programacionVuelo', function ($q) use ($programacion) {
                $q->where('estado', '!=', 'Salido')
                  ->whereRaw("CONCAT(fecha_salida, ' ', hora_salida) < ?", [$programacion->fecha_llegada . ' ' . $programacion->hora_llegada])
                  ->whereRaw("CONCAT(fecha_llegada, ' ', hora_llegada) > ?", [$programacion->fecha_salida . ' ' . $programacion->hora_salida]);
            })
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'Este empleado tiene un conflicto de horario con otro vuelo.');
        }

        Tripulacion::create([
            'programacion_vuelo_id' => $request->programacion_vuelo_id,
            'empleado_id' => $request->empleado_id,
            'cargo' => $request->cargo,
        ]);

        return redirect()->route('operador.tripulaciones.index', ['programacion_id' => $request->programacion_vuelo_id])
            ->with('success', 'Tripulante asignado exitosamente.');
    }

    public function destroy(Tripulacion $tripulacion)
    {
        $programacion_id = $tripulacion->programacion_vuelo_id;

        $tripulacion->delete();

        return redirect()->route('operador.tripulaciones.index', ['programacion_id' => $programacion_id])
            ->with('success', 'Tripulante removido exitosamente.');
    }
}