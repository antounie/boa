<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with([
            'programacionVuelo.aeropuertoOrigen',
            'programacionVuelo.aeropuertoDestino',
            'cliente',
            'tickets.asiento.tipoClase',
            'transacciones',
            'reserva'
        ]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_venta', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function ($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('apellido', 'like', "%{$buscar}%")
                         ->orWhere('documento_identidad', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('programacionVuelo', function ($q2) use ($buscar) {
                      $q2->where('codigo_vuelo', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $ventas = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalVentas = Venta::where('estado', 'Confirmada')->sum('monto_total');
        $cantidadVentas = Venta::where('estado', 'Confirmada')->count();
        $cantidadCanceladas = Venta::where('estado', 'Cancelada')->count();

        return view('admin.ventas.index', compact('ventas', 'totalVentas', 'cantidadVentas', 'cantidadCanceladas'));
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'programacionVuelo.aeropuertoOrigen',
            'programacionVuelo.aeropuertoDestino',
            'programacionVuelo.aeronave',
            'cliente',
            'tickets.asiento.tipoClase',
            'transacciones',
            'reserva',
            'devolucion.egreso'
        ]);

        return view('admin.ventas.show', compact('venta'));
    }
}