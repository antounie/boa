<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Aeronave;
use App\Models\Asiento;
use App\Models\AsientoProgramacion;
use App\Models\ProgramacionVuelo;
use App\Models\TipoClase;
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
        $tipoClases = TipoClase::orderBy('id')->get();
        return view('operador.aeronaves.create', compact('tipoClases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricula'           => 'required|string|max:20|unique:aeronaves',
            'modelo'              => 'required|string|max:80',
            'fabricante'          => 'required|string|max:80',
            'clases'                 => 'required|array|min:1',
            'clases.*.tipo_clase_id' => 'required|exists:tipo_clases,id',
            'clases.*.cantidad'      => 'required|integer|min:1',
            'clases.*.columnas'      => 'required|integer|min:1|max:10',
        ], [
            'matricula.unique'    => 'Esta matrícula ya está registrada.',
            'clases.required'     => 'Debes configurar al menos una clase de asiento.',
        ]);

        $totalAsientos = collect($request->clases)->sum('cantidad');

        $aeronave = Aeronave::create([
            'matricula'       => strtoupper($request->matricula),
            'modelo'          => $request->modelo,
            'fabricante'      => $request->fabricante,
            'capacidad_total' => $totalAsientos,
            'estado'          => 'Activa',
        ]);

        $this->generarAsientos($aeronave->id, $request->clases, 1);

        return redirect()->route('operador.aeronaves.index')
            ->with('success', "Aeronave {$aeronave->matricula} registrada con {$totalAsientos} asiento(s) generado(s).");
    }

    public function edit(Aeronave $aeronave)
    {
        $tipoClases = TipoClase::orderBy('id')->get();
        $resumenAsientos = Asiento::where('aeronave_id', $aeronave->id)
            ->with('tipoClase')
            ->get()
            ->groupBy('tipo_clase_id')
            ->map(fn($grupo) => [
                'tipoClase' => $grupo->first()->tipoClase,
                'cantidad'  => $grupo->count(),
                'columnas'  => $grupo->pluck('numero')->map(fn($n) => preg_replace('/[0-9]/', '', $n))->unique()->count() ?: 1,
            ]);
        $ultimaFila = Asiento::where('aeronave_id', $aeronave->id)->max('fila') ?? 0;
        $totalAsientosConfigurados = Asiento::where('aeronave_id', $aeronave->id)->count();
        return view('operador.aeronaves.edit', compact('aeronave', 'tipoClases', 'resumenAsientos', 'ultimaFila', 'totalAsientosConfigurados'));
    }

    public function update(Request $request, Aeronave $aeronave)
    {
        $asientosCount = Asiento::where('aeronave_id', $aeronave->id)->count();

        $request->validate([
            'matricula'       => ['required', 'string', 'max:20', Rule::unique('aeronaves')->ignore($aeronave->id)],
            'modelo'          => 'required|string|max:80',
            'fabricante'      => 'required|string|max:80',
            'capacidad_total' => 'sometimes|integer|min:' . $asientosCount,
        ], [
            'matricula.unique'        => 'Esta matrícula ya está registrada.',
            'capacidad_total.min'     => "La capacidad no puede ser menor a los {$asientosCount} asientos ya configurados.",
        ]);

        $updateData = [
            'matricula'  => strtoupper($request->matricula),
            'modelo'     => $request->modelo,
            'fabricante' => $request->fabricante,
        ];

        if ($request->has('capacidad_total')) {
            $updateData['capacidad_total'] = $request->capacidad_total;
        }

        $aeronave->update($updateData);

        // Agregar asientos nuevos si se enviaron
        if ($request->filled('clases')) {
            $request->validate([
                'clases.*.tipo_clase_id' => 'required|exists:tipo_clases,id',
                'clases.*.cantidad'      => 'required|integer|min:1',
                'clases.*.columnas'      => 'required|integer|min:1|max:10',
            ]);

            $ultimaFila = Asiento::where('aeronave_id', $aeronave->id)->max('fila') ?? 0;
            $nuevosAsientos = $this->generarAsientos($aeronave->id, $request->clases, $ultimaFila + 1);
            $aeronave->update(['capacidad_total' => Asiento::where('aeronave_id', $aeronave->id)->count()]);

            // Sync each new seat to active programaciones
            $progIds = ProgramacionVuelo::where('aeronave_id', $aeronave->id)
                ->whereIn('estado', ['Programado', 'Completo'])
                ->pluck('id');

            foreach ($nuevosAsientos as $asiento) {
                foreach ($progIds as $progId) {
                    AsientoProgramacion::firstOrCreate(
                        ['asiento_id' => $asiento->id, 'programacion_vuelo_id' => $progId],
                        ['estado' => 'Disponible']
                    );
                }
            }

            // Revert Completo → Programado for flights that now have available seats
            ProgramacionVuelo::where('aeronave_id', $aeronave->id)
                ->where('estado', 'Completo')
                ->get()
                ->each(function ($prog) {
                    $disponibles = AsientoProgramacion::where('programacion_vuelo_id', $prog->id)
                        ->where('estado', 'Disponible')->count();
                    if ($disponibles > 0) {
                        $prog->update(['estado' => 'Programado']);
                    }
                });

            return redirect()->route('operador.aeronaves.edit', $aeronave)
                ->with('success', count($nuevosAsientos) . " asiento(s) agregado(s) exitosamente.");
        }

        return redirect()->route('operador.aeronaves.index')
            ->with('success', 'Aeronave actualizada exitosamente.');
    }

    private function generarAsientos(int $aeronaveId, array $clases, int $filaInicio): array
    {
        $letras     = range('A', 'J');
        $filaActual = $filaInicio;
        $creados    = [];

        foreach ($clases as $claseData) {
            $cantidad      = (int) $claseData['cantidad'];
            $columnas      = min((int) ($claseData['columnas'] ?? 1), 10);
            $asientosEnFila = 0;

            for ($i = 0; $i < $cantidad; $i++) {
                $letra = $letras[$asientosEnFila % $columnas];
                if ($asientosEnFila > 0 && $asientosEnFila % $columnas === 0) {
                    $filaActual++;
                }
                $creados[] = Asiento::create([
                    'aeronave_id'   => $aeronaveId,
                    'numero'        => $filaActual . $letra,
                    'fila'          => $filaActual,
                    'tipo_clase_id' => $claseData['tipo_clase_id'],
                ]);
                $asientosEnFila++;
            }
            if ($asientosEnFila > 0) $filaActual++;
        }

        return $creados;
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