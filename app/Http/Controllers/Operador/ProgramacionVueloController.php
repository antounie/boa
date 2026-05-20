<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\ProgramacionPrecio;
use App\Models\RutaTramo;
use App\Models\Aeronave;
use App\Models\Asiento;
use App\Models\AsientoProgramacion;
use App\Models\TipoClase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramacionVueloController extends Controller
{
    private function calcularLlegada(string $fechaSalida, string $horaSalida, string $duracion): array
    {
        [$h, $m] = array_map('intval', explode(':', $duracion));
        $llegada = Carbon::parse($fechaSalida . ' ' . $horaSalida)->addHours($h)->addMinutes($m);
        return [
            'fecha_llegada' => $llegada->toDateString(),
            'hora_llegada'  => $llegada->format('H:i'),
        ];
    }

    public function index(Request $request)
    {
        $query = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'aeronave']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_vuelo', 'like', "%{$buscar}%")
                  ->orWhereHas('aeropuertoOrigen', function ($q2) use ($buscar) {
                      $q2->where('ciudad', 'like', "%{$buscar}%")
                         ->orWhere('codigo_IATA', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('aeropuertoDestino', function ($q2) use ($buscar) {
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
        $rutaTramos = RutaTramo::with([
            'ruta.aeropuertoOrigen',
            'ruta.aeropuertoDestino',
            'tramo.aeropuertoOrigen',
            'tramo.aeropuertoDestino',
            'tramo.subTramos.aeropuertoOrigen',
            'tramo.subTramos.aeropuertoDestino',
        ])->orderBy('ruta_id')->orderBy('orden')->get();

        $aeronaves = Aeronave::where('estado', 'Activa')->orderBy('matricula')->get();
        $tipoClases = TipoClase::orderBy('id')->get();
        return view('operador.programaciones.create', compact('rutaTramos', 'aeronaves', 'tipoClases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_vuelo'            => 'required|string|max:20|unique:programacion_vuelos,codigo_vuelo',
            'ruta_tramo_id'           => 'required|exists:ruta_tramo,id',
            'aeronave_id'             => 'required|exists:aeronaves,id',
            'fecha_salida'            => 'required|date|after_or_equal:today',
            'hora_salida'             => 'required|date_format:H:i',
            'precios'                 => 'required|array|min:1',
            'precios.*.tipo_clase_id' => 'required|exists:tipo_clases,id',
            'precios.*.precio'        => 'required|numeric|min:1',
        ], [
            'codigo_vuelo.unique'         => 'Este código de vuelo ya está registrado.',
            'fecha_salida.after_or_equal' => 'La fecha de salida debe ser hoy o posterior.',
            'hora_salida.date_format'     => 'La hora de salida debe tener el formato HH:MM.',
            'precios.required'            => 'Debes configurar el precio para al menos una clase.',
        ]);

        $rutaTramo = RutaTramo::with(['ruta', 'tramo'])->findOrFail($request->ruta_tramo_id);
        $llegada   = $this->calcularLlegada($request->fecha_salida, $request->hora_salida, $rutaTramo->tramo->duracion_estimada);

        $conflicto = ProgramacionVuelo::where('aeronave_id', $request->aeronave_id)
            ->where('estado', '!=', 'Salido')
            ->where(function ($q) use ($request, $llegada) {
                $q->whereRaw("CONCAT(fecha_salida, ' ', hora_salida) < ?", [$llegada['fecha_llegada'] . ' ' . $llegada['hora_llegada']])
                  ->whereRaw("CONCAT(fecha_llegada, ' ', hora_llegada) > ?", [$request->fecha_salida . ' ' . $request->hora_salida]);
            })->exists();

        if ($conflicto) {
            return back()->withErrors(['aeronave_id' => 'La aeronave tiene un conflicto de horario en esa fecha.'])->withInput();
        }

        $precioMinimo = collect($request->precios)->min('precio');

        $programacion = ProgramacionVuelo::create([
            'codigo_vuelo'          => strtoupper($request->codigo_vuelo),
            'ruta_tramo_id'         => $rutaTramo->id,
            'ruta_id'               => $rutaTramo->ruta_id,
            'aeronave_id'           => $request->aeronave_id,
            'aeropuerto_origen_id'  => $rutaTramo->tramo->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $rutaTramo->tramo->aeropuerto_destino_id,
            'fecha_salida'          => $request->fecha_salida,
            'hora_salida'           => $request->hora_salida,
            'fecha_llegada'         => $llegada['fecha_llegada'],
            'hora_llegada'          => $llegada['hora_llegada'],
            'precio_base'           => $precioMinimo,
            'asientos_vendidos'     => 0,
            'estado'                => 'Programado',
        ]);

        foreach ($request->precios as $p) {
            ProgramacionPrecio::create([
                'programacion_vuelo_id' => $programacion->id,
                'tipo_clase_id'         => $p['tipo_clase_id'],
                'precio'                => $p['precio'],
            ]);
        }

        $asientos = Asiento::where('aeronave_id', $request->aeronave_id)->get();
        foreach ($asientos as $asiento) {
            AsientoProgramacion::create([
                'asiento_id'            => $asiento->id,
                'programacion_vuelo_id' => $programacion->id,
                'estado'                => 'Disponible',
            ]);
        }

        return redirect()->route('operador.programaciones.index')
            ->with('success', "Programación {$programacion->codigo_vuelo} creada exitosamente. {$asientos->count()} asientos generados.");
    }

    public function show(ProgramacionVuelo $programacion)
    {
        $programacion->load(['aeropuertoOrigen', 'aeropuertoDestino', 'aeronave', 'tripulacion.empleado', 'rutaTramo.tramo.subTramos.aeropuertoOrigen', 'rutaTramo.tramo.subTramos.aeropuertoDestino']);
        return view('operador.programaciones.show', compact('programacion'));
    }

    public function edit(ProgramacionVuelo $programacion)
    {
        if ($programacion->estado !== 'Programado') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'Solo se pueden editar programaciones en estado "Programado".');
        }

        $rutaTramos = RutaTramo::with([
            'ruta.aeropuertoOrigen',
            'ruta.aeropuertoDestino',
            'tramo.aeropuertoOrigen',
            'tramo.aeropuertoDestino',
            'tramo.subTramos.aeropuertoOrigen',
            'tramo.subTramos.aeropuertoDestino',
        ])->orderBy('ruta_id')->orderBy('orden')->get();

        $aeronaves = Aeronave::where('estado', 'Activa')->orderBy('matricula')->get();
        $tipoClases = TipoClase::orderBy('id')->get();
        $programacion->load('precios');
        return view('operador.programaciones.edit', compact('programacion', 'rutaTramos', 'aeronaves', 'tipoClases'));
    }

    public function update(Request $request, ProgramacionVuelo $programacion)
    {
        if ($programacion->estado !== 'Programado') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'Solo se pueden editar programaciones en estado "Programado".');
        }

        $request->validate([
            'codigo_vuelo'            => 'required|string|max:20|unique:programacion_vuelos,codigo_vuelo,' . $programacion->id,
            'ruta_tramo_id'           => 'required|exists:ruta_tramo,id',
            'aeronave_id'             => 'required|exists:aeronaves,id',
            'fecha_salida'            => 'required|date',
            'hora_salida'             => 'required|date_format:H:i',
            'precios'                 => 'required|array|min:1',
            'precios.*.tipo_clase_id' => 'required|exists:tipo_clases,id',
            'precios.*.precio'        => 'required|numeric|min:1',
        ], [
            'codigo_vuelo.unique'     => 'Este código de vuelo ya está registrado.',
            'hora_salida.date_format' => 'La hora de salida debe tener el formato HH:MM.',
            'precios.required'        => 'Debes configurar el precio para al menos una clase.',
        ]);

        $rutaTramo = RutaTramo::with(['ruta', 'tramo'])->findOrFail($request->ruta_tramo_id);
        $llegada   = $this->calcularLlegada($request->fecha_salida, $request->hora_salida, $rutaTramo->tramo->duracion_estimada);

        $conflicto = ProgramacionVuelo::where('aeronave_id', $request->aeronave_id)
            ->where('id', '!=', $programacion->id)
            ->where('estado', '!=', 'Salido')
            ->where(function ($q) use ($request, $llegada) {
                $q->whereRaw("CONCAT(fecha_salida, ' ', hora_salida) < ?", [$llegada['fecha_llegada'] . ' ' . $llegada['hora_llegada']])
                  ->whereRaw("CONCAT(fecha_llegada, ' ', hora_llegada) > ?", [$request->fecha_salida . ' ' . $request->hora_salida]);
            })->exists();

        if ($conflicto) {
            return back()->withErrors(['aeronave_id' => 'La aeronave tiene un conflicto de horario en esa fecha.'])->withInput();
        }

        $precioMinimo = collect($request->precios)->min('precio');

        $programacion->update([
            'codigo_vuelo'          => strtoupper($request->codigo_vuelo),
            'ruta_tramo_id'         => $rutaTramo->id,
            'ruta_id'               => $rutaTramo->ruta_id,
            'aeronave_id'           => $request->aeronave_id,
            'aeropuerto_origen_id'  => $rutaTramo->tramo->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $rutaTramo->tramo->aeropuerto_destino_id,
            'fecha_salida'          => $request->fecha_salida,
            'hora_salida'           => $request->hora_salida,
            'fecha_llegada'         => $llegada['fecha_llegada'],
            'hora_llegada'          => $llegada['hora_llegada'],
            'precio_base'           => $precioMinimo,
        ]);

        foreach ($request->precios as $p) {
            ProgramacionPrecio::updateOrCreate(
                ['programacion_vuelo_id' => $programacion->id, 'tipo_clase_id' => $p['tipo_clase_id']],
                ['precio' => $p['precio']]
            );
        }

        return redirect()->route('operador.programaciones.index')
            ->with('success', 'Programación actualizada exitosamente.');
    }

    public function reprogramar(ProgramacionVuelo $programacion)
    {
        if ($programacion->estado === 'Salido') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'No se puede reprogramar un vuelo que ya salió.');
        }

        return view('operador.programaciones.reprogramar', compact('programacion'));
    }

    public function guardarReprogramacion(Request $request, ProgramacionVuelo $programacion)
    {
        if ($programacion->estado === 'Salido') {
            return redirect()->route('operador.programaciones.index')
                ->with('error', 'No se puede reprogramar un vuelo que ya salió.');
        }

        $request->validate([
            'fecha_salida'        => 'required|date',
            'hora_salida'         => 'required|date_format:H:i',
            'motivo_reprogramacion' => 'required|string|max:500',
        ], [
            'fecha_salida.required'           => 'La nueva fecha de salida es obligatoria.',
            'hora_salida.date_format'         => 'La hora debe tener el formato HH:MM.',
            'motivo_reprogramacion.required'  => 'El motivo de la reprogramación es obligatorio.',
        ]);

        // Guardar fecha/hora originales solo la primera vez
        $fechaOriginal = $programacion->fecha_original ?? $programacion->fecha_salida;
        $horaOriginal  = $programacion->hora_original  ?? $programacion->hora_salida;

        $rutaTramo = $programacion->rutaTramo()->with('tramo')->first();
        $llegada   = $this->calcularLlegada($request->fecha_salida, $request->hora_salida, $rutaTramo->tramo->duracion_estimada);

        $programacion->update([
            'fecha_salida'           => $request->fecha_salida,
            'hora_salida'            => $request->hora_salida,
            'fecha_llegada'          => $llegada['fecha_llegada'],
            'hora_llegada'           => $llegada['hora_llegada'],
            'fecha_original'         => $fechaOriginal,
            'hora_original'          => $horaOriginal,
            'motivo_reprogramacion'  => $request->motivo_reprogramacion,
            'estado'                 => $programacion->estado === 'Salido' ? 'Salido' : 'Programado',
        ]);

        return redirect()->route('operador.programaciones.show', $programacion)
            ->with('success', "Vuelo {$programacion->codigo_vuelo} reprogramado exitosamente.");
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
