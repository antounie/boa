<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Tramo;
use App\Models\Aeropuerto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TramoController extends Controller
{
    public function index(Request $request)
    {
        $query = Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'subTramos.aeropuertoOrigen', 'subTramos.aeropuertoDestino'])
                      ->whereNull('tramo_padre_id');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('aeropuertoOrigen', fn($q2) =>
                    $q2->where('ciudad', 'like', "%{$buscar}%")->orWhere('codigo_IATA', 'like', "%{$buscar}%")
                )->orWhereHas('aeropuertoDestino', fn($q2) =>
                    $q2->where('ciudad', 'like', "%{$buscar}%")->orWhere('codigo_IATA', 'like', "%{$buscar}%")
                );
            });
        }

        $tramos = $query->orderBy('id', 'desc')->paginate(10);
        return view('operador.tramos.index', compact('tramos'));
    }

    public function create()
    {
        $aeropuertos  = Aeropuerto::orderBy('ciudad')->get();
        $tramosPadres = Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])
                             ->whereNull('tramo_padre_id')
                             ->orderBy('id')
                             ->get();
        return view('operador.tramos.create', compact('aeropuertos', 'tramosPadres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aeropuerto_origen_id'  => 'required|exists:aeropuertos,id|different:aeropuerto_destino_id',
            'aeropuerto_destino_id' => 'required|exists:aeropuertos,id',
            'duracion_estimada'     => 'required',
            'tiempo_escala'         => 'nullable|date_format:H:i',
            'tramo_padre_id'        => 'nullable|exists:tramos,id',
            'orden'                 => 'required|integer|min:1',
        ], [
            'aeropuerto_origen_id.different' => 'El aeropuerto de origen y destino deben ser diferentes.',
        ]);

        // Si tiene padre, verificar que el padre sea un tramo raíz
        if ($request->filled('tramo_padre_id')) {
            $padre = Tramo::findOrFail($request->tramo_padre_id);
            if (!$padre->esPadre()) {
                return back()->withErrors(['tramo_padre_id' => 'El tramo padre debe ser un tramo raíz.'])->withInput();
            }
        }

        $tramo = Tramo::create([
            'tramo_padre_id'        => $request->tramo_padre_id ?: null,
            'aeropuerto_origen_id'  => $request->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $request->aeropuerto_destino_id,
            'duracion_estimada'     => $request->duracion_estimada,
            'tiempo_escala'         => $request->tiempo_escala ?: null,
            'orden'                 => $request->orden,
        ]);

        if ($tramo->tramo_padre_id) {
            Tramo::find($tramo->tramo_padre_id)->recalcularDuracion();
            return redirect()->route('operador.tramos.show', $tramo->tramo_padre_id)
                             ->with('success', 'Sub-tramo registrado exitosamente. Duración del tramo raíz recalculada.');
        }

        return redirect()->route('operador.tramos.index')
                         ->with('success', 'Tramo registrado exitosamente.');
    }

    public function show(Tramo $tramo)
    {
        $tramo->load([
            'aeropuertoOrigen',
            'aeropuertoDestino',
            'tramoPadre.aeropuertoOrigen',
            'tramoPadre.aeropuertoDestino',
            'subTramos.aeropuertoOrigen',
            'subTramos.aeropuertoDestino',
            'rutas.aeropuertoOrigen',
            'rutas.aeropuertoDestino',
        ]);
        return view('operador.tramos.show', compact('tramo'));
    }

    public function edit(Tramo $tramo)
    {
        $aeropuertos  = Aeropuerto::orderBy('ciudad')->get();
        $tramosPadres = Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])
                             ->whereNull('tramo_padre_id')
                             ->where('id', '!=', $tramo->id)
                             ->orderBy('id')
                             ->get();
        $tramo->load(['aeropuertoOrigen', 'aeropuertoDestino']);
        return view('operador.tramos.edit', compact('tramo', 'aeropuertos', 'tramosPadres'));
    }

    public function update(Request $request, Tramo $tramo)
    {
        $request->validate([
            'aeropuerto_origen_id'  => 'required|exists:aeropuertos,id|different:aeropuerto_destino_id',
            'aeropuerto_destino_id' => 'required|exists:aeropuertos,id',
            'duracion_estimada'     => 'required',
            'tiempo_escala'         => 'nullable|date_format:H:i',
            'tramo_padre_id'        => 'nullable|exists:tramos,id',
            'orden'                 => 'required|integer|min:1',
        ], [
            'aeropuerto_origen_id.different' => 'El aeropuerto de origen y destino deben ser diferentes.',
        ]);

        if ($request->filled('tramo_padre_id')) {
            if ($request->tramo_padre_id == $tramo->id) {
                return back()->withErrors(['tramo_padre_id' => 'Un tramo no puede ser su propio padre.'])->withInput();
            }
            $padre = Tramo::findOrFail($request->tramo_padre_id);
            if (!$padre->esPadre()) {
                return back()->withErrors(['tramo_padre_id' => 'El tramo padre debe ser un tramo raíz.'])->withInput();
            }
        }

        $tramo->update([
            'tramo_padre_id'        => $request->tramo_padre_id ?: null,
            'aeropuerto_origen_id'  => $request->aeropuerto_origen_id,
            'aeropuerto_destino_id' => $request->aeropuerto_destino_id,
            'duracion_estimada'     => $request->duracion_estimada,
            'tiempo_escala'         => $request->tiempo_escala ?: null,
            'orden'                 => $request->orden,
        ]);

        $padreId = $tramo->tramo_padre_id;
        if ($padreId) {
            Tramo::find($padreId)->recalcularDuracion();
            return redirect()->route('operador.tramos.show', $padreId)
                             ->with('success', 'Sub-tramo actualizado. Duración del tramo raíz recalculada.');
        }

        return redirect()->route('operador.tramos.index')
                         ->with('success', 'Tramo actualizado exitosamente.');
    }

    public function destroy(Tramo $tramo)
    {
        if ($tramo->rutas()->exists()) {
            return redirect()->route('operador.tramos.index')
                             ->with('error', 'No se puede eliminar este tramo porque está asignado a una o más rutas.');
        }

        if ($tramo->subTramos()->exists()) {
            return redirect()->route('operador.tramos.index')
                             ->with('error', 'No se puede eliminar este tramo porque tiene sub-tramos asociados.');
        }

        $tramo->delete();

        return redirect()->route('operador.tramos.index')
                         ->with('success', 'Tramo eliminado exitosamente.');
    }
}
