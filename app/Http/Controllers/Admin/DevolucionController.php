<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devolucion;
use App\Models\Egreso;
use App\Models\Venta;
use App\Models\Asiento;
use App\Models\ProgramacionVuelo;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\AsientoProgramacion;
use Illuminate\Support\Facades\DB;


class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $query = Devolucion::with([
            'venta.programacionVuelo.vuelo',
            'venta.programacionVuelo.ruta.aeropuertoOrigen',
            'venta.programacionVuelo.ruta.aeropuertoDestino',
            'cliente',
            'egreso'
        ]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('cliente', function ($q2) use ($buscar) {
                    $q2->where('nombre', 'like', "%{$buscar}%")
                       ->orWhere('apellido', 'like', "%{$buscar}%")
                       ->orWhere('documento_identidad', 'like', "%{$buscar}%");
                })->orWhereHas('venta', function ($q2) use ($buscar) {
                    $q2->where('codigo_venta', 'like', "%{$buscar}%");
                });
            });
        }

        $devoluciones = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.devoluciones.index', compact('devoluciones'));
    }

    public function create(Request $request)
    {
        $venta = null;
        $ventas = collect();

        if ($request->filled('buscar_venta')) {
            $buscar = $request->buscar_venta;
            $ventas = Venta::with([
                    'programacionVuelo.vuelo',
                    'programacionVuelo.ruta.aeropuertoOrigen',
                    'programacionVuelo.ruta.aeropuertoDestino',
                    'cliente',
                    'asiento.tipoClase',
                    'ticket'
                ])
                ->where('estado', 'Confirmada')
                ->whereDoesntHave('devolucion')
                ->whereHas('programacionVuelo', function ($q) {
                    $q->whereIn('estado', ['Programado', 'Completo', 'Cancelado']);
                })
                ->where(function ($q) use ($buscar) {
                    $q->where('codigo_venta', 'like', "%{$buscar}%")
                      ->orWhereHas('cliente', function ($q2) use ($buscar) {
                          $q2->where('nombre', 'like', "%{$buscar}%")
                             ->orWhere('apellido', 'like', "%{$buscar}%")
                             ->orWhere('documento_identidad', 'like', "%{$buscar}%");
                      });
                })
                ->get();
        }

        return view('admin.devoluciones.create', compact('ventas'));
    }

    public function confirmar(Venta $venta)
    {
        $venta->load([
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino',
            'cliente',
            'asiento.tipoClase',
            'ticket'
        ]);

        // Verificar que se pueda devolver
        if ($venta->estado !== 'Confirmada') {
            return redirect()->route('admin.devoluciones.create')
                ->with('error', 'Esta venta ya fue cancelada.');
        }

        if ($venta->programacionVuelo->estado === 'Salido') {
            return redirect()->route('admin.devoluciones.create')
                ->with('error', 'No se puede devolver un pasaje de un vuelo que ya partió.');
        }

        return view('admin.devoluciones.confirmar', compact('venta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'motivo' => 'required|string|max:300',
        ]);

        $venta = Venta::with(['programacionVuelo', 'asiento', 'ticket'])->find($request->venta_id);

        // Verificaciones previas a la transacción
        if ($venta->estado !== 'Confirmada') {
            return redirect()->route('admin.devoluciones.index')
                ->with('error', 'Esta venta ya fue cancelada.');
        }

        if ($venta->programacionVuelo->estado === 'Salido') {
            return redirect()->route('admin.devoluciones.index')
                ->with('error', 'No se puede devolver un pasaje de un vuelo que ya partió.');
        }

        try {
            DB::beginTransaction();

            $devolucion = Devolucion::create([
                'venta_id' => $venta->id,
                'cliente_id' => $venta->cliente_id,
                'monto_devolucion' => $venta->monto_total,
                'motivo' => $request->motivo,
            ]);

            $venta->update(['estado' => 'Cancelada']);

            if ($venta->ticket) {
                $venta->ticket->update(['estado' => 'Anulado']);
            }

            AsientoProgramacion::where('asiento_id', $venta->asiento_id)
                ->where('programacion_vuelo_id', $venta->programacion_vuelo_id)
                ->update(['estado' => 'Disponible']);

            $venta->programacionVuelo->decrement('asientos_vendidos');

            if ($venta->programacionVuelo->estado === 'Completo') {
                $venta->programacionVuelo->update(['estado' => 'Programado']);
            }

            if ($venta->reserva_id) {
                \App\Models\Reserva::where('id', $venta->reserva_id)->update(['estado' => 'Cancelada']);
            }

            Egreso::create([
                'devolucion_id' => $devolucion->id,
                'monto_devuelto' => $venta->monto_total,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.devoluciones.index')
                ->with('error', 'Error al procesar la devolución. Intente de nuevo.');
        }

        return redirect()->route('admin.devoluciones.index')
            ->with('success', "Devolución procesada exitosamente. Egreso de \${$venta->monto_total} generado automáticamente.");
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load([
            'venta.programacionVuelo.vuelo',
            'venta.programacionVuelo.ruta.aeropuertoOrigen',
            'venta.programacionVuelo.ruta.aeropuertoDestino',
            'venta.asiento.tipoClase',
            'venta.ticket',
            'cliente',
            'egreso'
        ]);

        return view('admin.devoluciones.show', compact('devolucion'));
    }
}