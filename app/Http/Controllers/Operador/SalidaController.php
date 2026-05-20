<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Salida;
use App\Models\Ingreso;
use App\Models\ProgramacionVuelo;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        // Vuelos elegibles para salida (estado Completo = todos los asientos vendidos)
        $vuelosElegibles = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'aeronave'])
            ->where('estado', 'Completo')
            ->whereDoesntHave('salida')
            ->orderBy('fecha_salida')
            ->get();

        // Historial de salidas registradas
        $query = Salida::with(['programacionVuelo.aeropuertoOrigen', 'programacionVuelo.aeropuertoDestino', 'ingreso']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('programacionVuelo', function ($q) use ($buscar) {
                $q->where('codigo_vuelo', 'like', "%{$buscar}%");
            });
        }

        $salidas = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('operador.salidas.index', compact('vuelosElegibles', 'salidas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
        ]);

        $programacion = ProgramacionVuelo::with('aeronave')->find($request->programacion_vuelo_id);

        // Verificar que el vuelo esté completo
        if ($programacion->estado !== 'Completo') {
            return redirect()->route('operador.salidas.index')
                ->with('error', 'Solo se pueden registrar salidas de vuelos con todos los asientos vendidos.');
        }

        // Verificar que no tenga salida ya registrada
        if (Salida::where('programacion_vuelo_id', $programacion->id)->exists()) {
            return redirect()->route('operador.salidas.index')
                ->with('error', 'Este vuelo ya tiene una salida registrada.');
        }

        $montoTotal = Venta::where('programacion_vuelo_id', $programacion->id)
            ->where('estado', 'Confirmada')
            ->sum('monto_total');

        $cantidadPasajes = Venta::where('programacion_vuelo_id', $programacion->id)
            ->where('estado', 'Confirmada')
            ->count();

        try {
            DB::beginTransaction();

            $salida = Salida::create([
                'programacion_vuelo_id' => $programacion->id,
                'monto_total_recaudado' => $montoTotal,
            ]);

            $programacion->update(['estado' => 'Salido']);

            Ingreso::create([
                'salida_id' => $salida->id,
                'programacion_vuelo_id' => $programacion->id,
                'monto_total' => $montoTotal,
                'cantidad_pasajes' => $cantidadPasajes,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('operador.salidas.index')
                ->with('error', 'Error al registrar la salida. Intente de nuevo.');
        }

        return redirect()->route('operador.salidas.index')
            ->with('success', "Salida registrada exitosamente. Ingreso de \${$montoTotal} generado automáticamente por {$cantidadPasajes} pasaje(s).");
    }

    public function show(Salida $salida)
    {
        $salida->load([
            'programacionVuelo.aeropuertoOrigen',
            'programacionVuelo.aeropuertoDestino',
            'programacionVuelo.aeronave',
            'ingreso'
        ]);

        $ventas = Venta::with(['cliente', 'tickets.asiento.tipoClase'])
            ->where('programacion_vuelo_id', $salida->programacion_vuelo_id)
            ->where('estado', 'Confirmada')
            ->get();

        return view('operador.salidas.show', compact('salida', 'ventas'));
    }
}
