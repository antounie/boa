<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingreso::with([
            'salida',
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('programacionVuelo.vuelo', function ($q) use ($buscar) {
                $q->where('codigo_vuelo', 'like', "%{$buscar}%");
            });
        }

        $ingresos = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalIngresos = Ingreso::sum('monto_total');
        $totalPasajes = Ingreso::sum('cantidad_pasajes');

        return view('admin.ingresos.index', compact('ingresos', 'totalIngresos', 'totalPasajes'));
    }

    public function show(Ingreso $ingreso)
    {
        $ingreso->load([
            'salida',
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino',
            'programacionVuelo.aeronave'
        ]);

        return view('admin.ingresos.show', compact('ingreso'));
    }
}
