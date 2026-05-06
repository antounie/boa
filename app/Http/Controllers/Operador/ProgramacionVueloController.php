<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Vuelo;
use App\Models\Ruta;
use App\Models\Aeronave;
use Illuminate\Http\Request;
use App\Models\AsientoProgramacion;
use App\Models\Asiento;

class ProgramacionVueloController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramacionVuelo::with(['vuelo.vueloPadre', 'vuelo.escalas', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('vuelo', function ($q2) use ($buscar) {
                    $q2->where('codigo_vuelo', 'like', "%{$buscar}%");
                })->orWhereHas('ruta.aeropuertoOrigen', function ($q2) use ($buscar) {
                    $q2->where('ciudad', 'like', "%{$buscar}%")
                       ->orWhere('codigo_IATA', 'like', "%{$buscar}%");
                })->orWhereHas('ruta.aeropuertoDestino', function ($q2) use ($buscar) {
                    $q2->where('ciudad', 'like', "%{$buscar}%")
                       ->orWhere('codigo_IATA', 'like', "%{$buscar}%");
                });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $programaciones = $query->orderBy('fecha_salida', 'desc')->paginate(10);
        return view('operador.programaciones.index', compact('programaciones'));
    }

   public function create()
    {
        $vuelos = Vuelo::with('vueloPadre')
            ->where('estado', 'Activo')
            ->orderBy('codigo_vuelo')
            ->get();
        $rutas = Ruta::with(['aeropuertoOrigen', 'aeropuertoDestino'])->get();
        $aeronaves = Aeronave::where('estado', 'Activa')->orderBy('matricula')->get();

        // Obtener horarios de tramos para vuelos padre ConEscalas
        $horariosPadre = [];
        $vuelosPadre = Vuelo::where('tipo', 'ConEscalas')->whereNull('vuelo_padre_id')->get();

        foreach ($vuelosPadre as $padre) {
            $hijosIds = Vuelo::where('vuelo_padre_id', $padre->id)->pluck('id');
            $tramos = ProgramacionVuelo::whereIn('vuelo_id', $hijosIds)
                ->orderBy('fecha_salida')
                ->orderBy('hora_salida')
                ->get();

            if ($tramos->count() > 0) {
                $primerTramo = $tramos->first();
                $ultimoTramo = $tramos->last();
                $horariosPadre[$padre->id] = [
                    'fecha_salida' => $primerTramo->fecha_salida,
                    'hora_salida' => $primerTramo->hora_salida,
                    'fecha_llegada' => $ultimoTramo->fecha_llegada,
                    'hora_llegada' => $ultimoTramo->hora_llegada,
                    'aeronave_id' => $primerTramo->aeronave_id,
                ];
            }
        }

        return view('operador.programaciones.create', compact('vuelos', 'rutas', 'aeronaves', 'horariosPadre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vuelo_id' => 'required|exists:vuelos,id',
            'ruta_id' => 'required|exists:rutas,id',
            'aeronave_id' => 'required|exists:aeronaves,id',
            'fecha_salida' => 'required|date|after_or_equal:today',
            'hora_salida' => 'required|date_format:H:i',
            'fecha_llegada' => 'required|date|after_or_equal:fecha_salida',
            'hora_llegada' => 'required|date_format:H:i',
            'precio_base' => 'required|numeric|min:1',
        ], [
            'fecha_salida.after_or_equal' => 'La fecha de salida debe ser hoy o posterior.',
            'fecha_llegada.after_or_equal' => 'La fecha de llegada debe ser igual o posterior a la salida.',
            'hora_salida.date_format' => 'La hora de salida debe tener el formato HH:MM.',
            'hora_llegada.date_format' => 'La hora de llegada debe tener el formato HH:MM.',
            'precio_base.min' => 'El precio debe ser mayor a 0.',
        ]);

        // Verificar conflicto de horario de la aeronave
        $vuelo = Vuelo::find($request->vuelo_id);
        $queryConflicto = ProgramacionVuelo::where('aeronave_id', $request->aeronave_id)
            ->where('estado', '!=', 'Salido')
            ->where(function ($q) use ($request) {
                $q->whereRaw("CONCAT(fecha_salida, ' ', hora_salida) < ?", [$request->fecha_llegada . ' ' . $request->hora_llegada])
                  ->whereRaw("CONCAT(fecha_llegada, ' ', hora_llegada) > ?", [$request->fecha_salida . ' ' . $request->hora_salida]);
            });

        // Si es vuelo padre ConEscalas, excluir sus propios tramos hijos
        if ($vuelo->tipo === 'ConEscalas' && !$vuelo->vuelo_padre_id) {
            $hijosIds = Vuelo::where('vuelo_padre_id', $vuelo->id)->pluck('id');
            $queryConflicto->whereNotIn('vuelo_id', $hijosIds);
        }

        // Si es tramo hijo, excluir el vuelo padre y otros tramos del mismo padre
        if ($vuelo->vuelo_padre_id) {
            $hermanosIds = Vuelo::where('vuelo_padre_id', $vuelo->vuelo_padre_id)
                ->pluck('id')
                ->push($vuelo->vuelo_padre_id);
            $queryConflicto->whereNotIn('vuelo_id', $hermanosIds);
        }

        $conflicto = $queryConflicto->exists();

        if ($conflicto) {
            return back()->withErrors(['aeronave_id' => 'La aeronave tiene un conflicto de horario en esa fecha.'])->withInput();
        }

        $programacion = ProgramacionVuelo::create([
            'vuelo_id' => $request->vuelo_id,
            'ruta_id' => $request->ruta_id,
            'aeronave_id' => $request->aeronave_id,
            'fecha_salida' => $request->fecha_salida,
            'hora_salida' => $request->hora_salida,
            'fecha_llegada' => $request->fecha_llegada,
            'hora_llegada' => $request->hora_llegada,
            'precio_base' => $request->precio_base,
            'asientos_vendidos' => 0,
            'estado' => 'Programado',
        ]);

        // Generar asientos disponibles para esta programación
        $asientos = Asiento::where('aeronave_id', $request->aeronave_id)->get();
        foreach ($asientos as $asiento) {
            AsientoProgramacion::create([
                'asiento_id' => $asiento->id,
                'programacion_vuelo_id' => $programacion->id,
                'estado' => 'Disponible',
            ]);
        }

        $this->actualizarHorarioPadre($request->vuelo_id);

        return redirect()->route('operador.programaciones.index')
            ->with('success', "Programación de vuelo creada exitosamente. {$asientos->count()} asientos disponibles generados.");
    }

    public function show(ProgramacionVuelo $programacion)
    {
        $programacion->load(['vuelo.vueloPadre', 'vuelo.escalas', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave', 'tripulacion.empleado']);

        $tramosEscala = collect();

        // Si es un vuelo padre ConEscalas, obtener los tramos hijos
        if ($programacion->vuelo->tipo === 'ConEscalas' && !$programacion->vuelo->vuelo_padre_id) {
            $hijosIds = Vuelo::where('vuelo_padre_id', $programacion->vuelo->id)->pluck('id');

            $tramosEscala = ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino'])
                ->whereIn('vuelo_id', $hijosIds)
                ->orderBy('fecha_salida')
                ->orderBy('hora_salida')
                ->get();
        }

        // Si es un tramo hijo, obtener todos los tramos del mismo padre
        if ($programacion->vuelo->vuelo_padre_id) {
            $vueloPadreId = $programacion->vuelo->vuelo_padre_id;
            $hijosIds = Vuelo::where('vuelo_padre_id', $vueloPadreId)->pluck('id');

            $tramosEscala = ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino'])
                ->whereIn('vuelo_id', $hijosIds)
                ->orderBy('fecha_salida')
                ->orderBy('hora_salida')
                ->get();
        }

        return view('operador.programaciones.show', compact('programacion', 'tramosEscala'));
    }

    public function edit(ProgramacionVuelo $programacion)
    {
        if ($programacion->estado !== 'Programado') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'Solo se pueden editar programaciones en estado "Programado".');
        }

        $vuelos = Vuelo::with('vueloPadre')->where('estado', 'Activo')->orderBy('codigo_vuelo')->get();
        $rutas = Ruta::with(['aeropuertoOrigen', 'aeropuertoDestino'])->get();
        $aeronaves = Aeronave::where('estado', 'Activa')->orderBy('matricula')->get();
        return view('operador.programaciones.edit', compact('programacion', 'vuelos', 'rutas', 'aeronaves'));
    }

    public function update(Request $request, ProgramacionVuelo $programacion)
    {
        if ($programacion->estado !== 'Programado') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'Solo se pueden editar programaciones en estado "Programado".');
        }

        $request->validate([
            'vuelo_id' => 'required|exists:vuelos,id',
            'ruta_id' => 'required|exists:rutas,id',
            'aeronave_id' => 'required|exists:aeronaves,id',
            'fecha_salida' => 'required|date',
            'hora_salida' => 'required|date_format:H:i',
            'fecha_llegada' => 'required|date|after_or_equal:fecha_salida',
            'hora_llegada' => 'required|date_format:H:i',
            'precio_base' => 'required|numeric|min:1',
        ], [
            'hora_salida.date_format' => 'La hora de salida debe tener el formato HH:MM.',
            'hora_llegada.date_format' => 'La hora de llegada debe tener el formato HH:MM.',
        ]);

        // Verificar conflicto excluyendo la programación actual
        $vuelo = Vuelo::find($request->vuelo_id);
        $queryConflicto = ProgramacionVuelo::where('aeronave_id', $request->aeronave_id)
            ->where('id', '!=', $programacion->id)
            ->where('estado', '!=', 'Salido')
            ->where(function ($q) use ($request) {
                $q->whereRaw("CONCAT(fecha_salida, ' ', hora_salida) < ?", [$request->fecha_llegada . ' ' . $request->hora_llegada])
                  ->whereRaw("CONCAT(fecha_llegada, ' ', hora_llegada) > ?", [$request->fecha_salida . ' ' . $request->hora_salida]);
            });

        if ($vuelo->tipo === 'ConEscalas' && !$vuelo->vuelo_padre_id) {
            $hijosIds = Vuelo::where('vuelo_padre_id', $vuelo->id)->pluck('id');
            $queryConflicto->whereNotIn('vuelo_id', $hijosIds);
        }

        if ($vuelo->vuelo_padre_id) {
            $hermanosIds = Vuelo::where('vuelo_padre_id', $vuelo->vuelo_padre_id)
                ->pluck('id')
                ->push($vuelo->vuelo_padre_id);
            $queryConflicto->whereNotIn('vuelo_id', $hermanosIds);
        }

        $conflicto = $queryConflicto->exists();

        if ($conflicto) {
            return back()->withErrors(['aeronave_id' => 'La aeronave tiene un conflicto de horario en esa fecha.'])->withInput();
        }

        $programacion->update([
            'vuelo_id' => $request->vuelo_id,
            'ruta_id' => $request->ruta_id,
            'aeronave_id' => $request->aeronave_id,
            'fecha_salida' => $request->fecha_salida,
            'hora_salida' => $request->hora_salida,
            'fecha_llegada' => $request->fecha_llegada,
            'hora_llegada' => $request->hora_llegada,
            'precio_base' => $request->precio_base,
        ]);

        $this->actualizarHorarioPadre($request->vuelo_id);

        return redirect()->route('operador.programaciones.index')
            ->with('success', 'Programación actualizada exitosamente.');
    }

    private function actualizarHorarioPadre($vuelo_id)
    {
        $vuelo = Vuelo::find($vuelo_id);

        // Si es un tramo hijo, actualizar la programación del padre
        if ($vuelo && $vuelo->vuelo_padre_id) {
            $vueloPadre = $vuelo->vueloPadre;

            // Obtener todos los tramos hijos programados
            $hijosIds = Vuelo::where('vuelo_padre_id', $vueloPadre->id)->pluck('id');
            $tramos = ProgramacionVuelo::whereIn('vuelo_id', $hijosIds)
                ->orderBy('fecha_salida')
                ->orderBy('hora_salida')
                ->get();

            if ($tramos->count() > 0) {
                // Obtener la programación del vuelo padre
                $progPadre = ProgramacionVuelo::where('vuelo_id', $vueloPadre->id)->first();

                if ($progPadre) {
                    $ultimoTramo = $tramos->last();
                    $primerTramo = $tramos->first();

                    $progPadre->update([
                        'fecha_salida' => $primerTramo->fecha_salida,
                        'hora_salida' => $primerTramo->hora_salida,
                        'fecha_llegada' => $ultimoTramo->fecha_llegada,
                        'hora_llegada' => $ultimoTramo->hora_llegada,
                    ]);
                }
            }
        }
    }

    public function destroy(ProgramacionVuelo $programacion)
    {
        if ($programacion->asientos_vendidos > 0) {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'No se puede eliminar una programación que tiene ventas realizadas.');
        }

        $programacion->delete();

        return redirect()->route('operador.programaciones.index')
            ->with('success', 'Programación eliminada exitosamente.');
    }
}