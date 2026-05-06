<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\Aeronave;
use App\Models\TipoClase;
use Illuminate\Http\Request;
use App\Models\AsientoProgramacion;

class AsientoController extends Controller
{
    public function index(Request $request)
    {
        $aeronaves = Aeronave::withCount('asientos')->where('estado', 'Activa')->orderBy('matricula')->get();
        $aeronaveSeleccionada = null;
        $asientos = collect();

        if ($request->filled('aeronave_id')) {
            $aeronaveSeleccionada = Aeronave::find($request->aeronave_id);
            $asientos = Asiento::with('tipoClase')
                ->where('aeronave_id', $request->aeronave_id)
                ->orderBy('fila')
                ->orderBy('numero')
                ->get();
        }

        return view('operador.asientos.index', compact('aeronaves', 'aeronaveSeleccionada', 'asientos'));
    }

    public function create(Request $request)
    {
        $aeronave = Aeronave::findOrFail($request->aeronave_id);
        $tipoClases = TipoClase::all();
        $asientosActuales = Asiento::where('aeronave_id', $aeronave->id)->count();

        return view('operador.asientos.create', compact('aeronave', 'tipoClases', 'asientosActuales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aeronave_id' => 'required|exists:aeronaves,id',
            'numero' => 'required|string|max:10',
            'fila' => 'required|integer|min:1',
            'tipo_clase_id' => 'required|exists:tipo_clases,id',
        ]);

        $aeronave = Aeronave::find($request->aeronave_id);
        $asientosActuales = Asiento::where('aeronave_id', $aeronave->id)->count();

        // Verificar que no exceda la capacidad
        if ($asientosActuales >= $aeronave->capacidad_total) {
            return back()->withErrors(['numero' => "La aeronave {$aeronave->matricula} ya tiene todos sus asientos configurados ({$aeronave->capacidad_total})."])->withInput();
        }

        // Verificar que no exista el mismo número en la aeronave
        $existe = Asiento::where('aeronave_id', $request->aeronave_id)
            ->where('numero', $request->numero)
            ->exists();

        if ($existe) {
            return back()->withErrors(['numero' => 'Este número de asiento ya existe en esta aeronave.'])->withInput();
        }

        Asiento::create([
            'aeronave_id' => $request->aeronave_id,
            'numero' => strtoupper($request->numero),
            'fila' => $request->fila,
            'tipo_clase_id' => $request->tipo_clase_id,
        ]);

        return redirect()->route('operador.asientos.index', ['aeronave_id' => $request->aeronave_id])
            ->with('success', 'Asiento registrado exitosamente.');
    }

    public function edit(Asiento $asiento)
    {
        $tipoClases = TipoClase::all();
        return view('operador.asientos.edit', compact('asiento', 'tipoClases'));
    }

    public function update(Request $request, Asiento $asiento)
    {
        $request->validate([
            'numero' => 'required|string|max:10',
            'fila' => 'required|integer|min:1',
            'tipo_clase_id' => 'required|exists:tipo_clases,id',
        ]);

        $existe = Asiento::where('aeronave_id', $asiento->aeronave_id)
            ->where('numero', $request->numero)
            ->where('id', '!=', $asiento->id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['numero' => 'Este número de asiento ya existe en esta aeronave.'])->withInput();
        }

        $asiento->update([
            'numero' => strtoupper($request->numero),
            'fila' => $request->fila,
            'tipo_clase_id' => $request->tipo_clase_id,
        ]);

        return redirect()->route('operador.asientos.index', ['aeronave_id' => $asiento->aeronave_id])
            ->with('success', 'Asiento actualizado exitosamente.');
    }

    public function destroy(Asiento $asiento)
    {
        $aeronave_id = $asiento->aeronave_id;

        $ocupado = AsientoProgramacion::where('asiento_id', $asiento->id)->where('estado', 'Ocupado')->exists();
        if ($ocupado) {
            return redirect()->route('operador.asientos.index', ['aeronave_id' => $aeronave_id])
                ->with('error', 'No se puede eliminar un asiento que está ocupado en alguna programación.');
        }

        $asiento->delete();

        return redirect()->route('operador.asientos.index', ['aeronave_id' => $aeronave_id])
            ->with('success', 'Asiento eliminado exitosamente.');
    }

    public function generarMasivo(Request $request)
    {
        $request->validate([
            'aeronave_id' => 'required|exists:aeronaves,id',
            'columnas' => 'required|string|max:10',
        ]);

        $aeronave = Aeronave::find($request->aeronave_id);
        $columnas = str_split(strtoupper($request->columnas));
        $clases = $request->input('clases', []);
        $asientosActuales = Asiento::where('aeronave_id', $aeronave->id)->count();

        // Calcular total de asientos a generar
        $totalGenerar = 0;
        $filasConfig = [];

        foreach ($clases as $claseId => $config) {
            if (isset($config['activo']) && isset($config['fila_inicio']) && isset($config['fila_fin'])) {
                $inicio = (int) $config['fila_inicio'];
                $fin = (int) $config['fila_fin'];

                if ($inicio > 0 && $fin >= $inicio) {
                    for ($fila = $inicio; $fila <= $fin; $fila++) {
                        $filasConfig[$fila] = $claseId;
                        $totalGenerar += count($columnas);
                    }
                }
            }
        }

        if ($totalGenerar === 0) {
            return back()->with('error', 'No se configuraron filas para generar asientos.');
        }

        if (($asientosActuales + $totalGenerar) > $aeronave->capacidad_total) {
            return back()->with('error', "Se generarían {$totalGenerar} asientos pero la aeronave solo permite " . ($aeronave->capacidad_total - $asientosActuales) . " más.");
        }

        // Verificar solapamiento de filas entre clases
        $filasUsadas = [];
        foreach ($clases as $claseId => $config) {
            if (isset($config['activo']) && isset($config['fila_inicio']) && isset($config['fila_fin'])) {
                $inicio = (int) $config['fila_inicio'];
                $fin = (int) $config['fila_fin'];
                for ($fila = $inicio; $fila <= $fin; $fila++) {
                    if (in_array($fila, $filasUsadas)) {
                        return back()->with('error', "La fila {$fila} está asignada a más de una clase.");
                    }
                    $filasUsadas[] = $fila;
                }
            }
        }

        $creados = 0;
        ksort($filasConfig);

        foreach ($filasConfig as $fila => $claseId) {
            foreach ($columnas as $col) {
                $numero = $fila . $col;
                $existe = Asiento::where('aeronave_id', $aeronave->id)
                    ->where('numero', $numero)
                    ->exists();

                if (!$existe) {
                    Asiento::create([
                        'aeronave_id' => $aeronave->id,
                        'numero' => $numero,
                        'fila' => $fila,
                        'tipo_clase_id' => $claseId,
                    ]);
                    $creados++;
                }
            }
        }

        return redirect()->route('operador.asientos.index', ['aeronave_id' => $aeronave->id])
            ->with('success', "{$creados} asientos generados exitosamente.");
    }

    public function eliminarTodos(Request $request)
    {
        $aeronave_id = $request->aeronave_id;

        // Verificar que no haya asientos ocupados en alguna programación
        $ocupados = AsientoProgramacion::whereHas('asiento', function ($q) use ($aeronave_id) {
            $q->where('aeronave_id', $aeronave_id);
        })->where('estado', 'Ocupado')->count();

        if ($ocupados > 0) {
            return redirect()->route('operador.asientos.index', ['aeronave_id' => $aeronave_id])
                ->with('error', "No se pueden eliminar los asientos porque hay {$ocupados} asiento(s) ocupado(s) en programaciones de vuelo.");
        }

        $eliminados = Asiento::where('aeronave_id', $aeronave_id)->delete();

        return redirect()->route('operador.asientos.index', ['aeronave_id' => $aeronave_id])
            ->with('success', "{$eliminados} asientos eliminados exitosamente.");
    }
}