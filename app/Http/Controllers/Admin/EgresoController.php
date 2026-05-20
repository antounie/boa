<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    public function index(Request $request)
    {
        $query = Egreso::with([
            'devolucion.venta.programacionVuelo.aeropuertoOrigen',
            'devolucion.venta.programacionVuelo.aeropuertoDestino',
            'devolucion.cliente'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('devolucion.cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%");
            })->orWhereHas('devolucion.venta', function ($q) use ($buscar) {
                $q->where('codigo_venta', 'like', "%{$buscar}%");
            });
        }

        $egresos = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalEgresos = Egreso::sum('monto_devuelto');
        $totalDevoluciones = Egreso::count();

        return view('admin.egresos.index', compact('egresos', 'totalEgresos', 'totalDevoluciones'));
    }

    public function show(Egreso $egreso)
    {
        $egreso->load([
            'devolucion.venta.programacionVuelo.aeropuertoOrigen',
            'devolucion.venta.programacionVuelo.aeropuertoDestino',
            'devolucion.venta.tickets.asiento.tipoClase',
            'devolucion.cliente'
        ]);

        return view('admin.egresos.show', compact('egreso'));
    }
}
