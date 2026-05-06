<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Venta;
use App\Models\Ingreso;
use App\Models\Egreso;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    // ===== REPORTE DE VUELOS =====
    public function vuelos(Request $request)
    {
        $query = ProgramacionVuelo::with([
            'vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_salida', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_salida', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $programaciones = $query->orderBy('fecha_salida', 'desc')->get();

        $totales = [
            'programados' => $programaciones->where('estado', 'Programado')->count(),
            'completos' => $programaciones->where('estado', 'Completo')->count(),
            'salidos' => $programaciones->where('estado', 'Salido')->count(),
            'total' => $programaciones->count(),
        ];

        return view('admin.reportes.vuelos', compact('programaciones', 'totales'));
    }

    public function vuelosPdf(Request $request)
    {
        $query = ProgramacionVuelo::with([
            'vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_salida', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_salida', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $programaciones = $query->orderBy('fecha_salida', 'desc')->get();

        $totales = [
            'programados' => $programaciones->where('estado', 'Programado')->count(),
            'completos' => $programaciones->where('estado', 'Completo')->count(),
            'salidos' => $programaciones->where('estado', 'Salido')->count(),
            'total' => $programaciones->count(),
        ];

        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => $request->estado,
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.vuelos', compact('programaciones', 'totales', 'filtros'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_vuelos_' . date('Y-m-d_H-i') . '.pdf');
    }

    // ===== REPORTE DE VENTAS =====
    public function ventas(Request $request)
    {
        $query = Venta::with([
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino',
            'cliente', 'asiento.tipoClase'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'confirmadas' => $ventas->where('estado', 'Confirmada')->count(),
            'canceladas' => $ventas->where('estado', 'Cancelada')->count(),
            'monto_confirmadas' => $ventas->where('estado', 'Confirmada')->sum('monto_total'),
            'monto_canceladas' => $ventas->where('estado', 'Cancelada')->sum('monto_total'),
            'total' => $ventas->count(),
        ];

        return view('admin.reportes.ventas', compact('ventas', 'totales'));
    }

    public function ventasPdf(Request $request)
    {
        $query = Venta::with([
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino',
            'cliente', 'asiento.tipoClase'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'confirmadas' => $ventas->where('estado', 'Confirmada')->count(),
            'canceladas' => $ventas->where('estado', 'Cancelada')->count(),
            'monto_confirmadas' => $ventas->where('estado', 'Confirmada')->sum('monto_total'),
            'monto_canceladas' => $ventas->where('estado', 'Cancelada')->sum('monto_total'),
            'total' => $ventas->count(),
        ];

        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => $request->estado,
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.ventas', compact('ventas', 'totales', 'filtros'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_ventas_' . date('Y-m-d_H-i') . '.pdf');
    }

    // ===== REPORTE DE INGRESOS =====
    public function ingresos(Request $request)
    {
        $query = Ingreso::with([
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

        $ingresos = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'monto_total' => $ingresos->sum('monto_total'),
            'pasajes_total' => $ingresos->sum('cantidad_pasajes'),
            'cantidad' => $ingresos->count(),
        ];

        return view('admin.reportes.ingresos', compact('ingresos', 'totales'));
    }

    public function ingresosPdf(Request $request)
    {
        $query = Ingreso::with([
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

        $ingresos = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'monto_total' => $ingresos->sum('monto_total'),
            'pasajes_total' => $ingresos->sum('cantidad_pasajes'),
            'cantidad' => $ingresos->count(),
        ];

        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.ingresos', compact('ingresos', 'totales', 'filtros'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_ingresos_' . date('Y-m-d_H-i') . '.pdf');
    }

    // ===== REPORTE DE EGRESOS =====
    public function egresos(Request $request)
    {
        $query = Egreso::with([
            'devolucion.venta.programacionVuelo.vuelo',
            'devolucion.cliente'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $egresos = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'monto_total' => $egresos->sum('monto_devuelto'),
            'cantidad' => $egresos->count(),
        ];

        return view('admin.reportes.egresos', compact('egresos', 'totales'));
    }

    public function egresosPdf(Request $request)
    {
        $query = Egreso::with([
            'devolucion.venta.programacionVuelo.vuelo',
            'devolucion.cliente'
        ]);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $egresos = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'monto_total' => $egresos->sum('monto_devuelto'),
            'cantidad' => $egresos->count(),
        ];

        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.egresos', compact('egresos', 'totales', 'filtros'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_egresos_' . date('Y-m-d_H-i') . '.pdf');
    }

    public function enviarPorCorreo(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:vuelos,ventas,ingresos,egresos',
            'email' => 'required|email',
        ]);

        $tipo = $request->tipo;
        $email = $request->email;
        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => $request->estado,
        ];

        // Generar PDF según tipo
        switch ($tipo) {
            case 'vuelos':
                $query = \App\Models\ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave']);
                if ($request->filled('fecha_inicio')) $query->whereDate('fecha_salida', '>=', $request->fecha_inicio);
                if ($request->filled('fecha_fin')) $query->whereDate('fecha_salida', '<=', $request->fecha_fin);
                if ($request->filled('estado')) $query->where('estado', $request->estado);
                $programaciones = $query->orderBy('fecha_salida', 'desc')->get();
                $totales = [
                    'programados' => $programaciones->where('estado', 'Programado')->count(),
                    'completos' => $programaciones->where('estado', 'Completo')->count(),
                    'salidos' => $programaciones->where('estado', 'Salido')->count(),
                    'total' => $programaciones->count(),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.vuelos', compact('programaciones', 'totales', 'filtros'));
                $nombreArchivo = 'reporte_vuelos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Vuelos - BoA';
                break;

            case 'ventas':
                $query = \App\Models\Venta::with(['programacionVuelo.vuelo', 'programacionVuelo.ruta.aeropuertoOrigen', 'programacionVuelo.ruta.aeropuertoDestino', 'cliente', 'asiento.tipoClase']);
                if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
                if ($request->filled('fecha_fin')) $query->whereDate('created_at', '<=', $request->fecha_fin);
                if ($request->filled('estado')) $query->where('estado', $request->estado);
                $ventas = $query->orderBy('created_at', 'desc')->get();
                $totales = [
                    'confirmadas' => $ventas->where('estado', 'Confirmada')->count(),
                    'canceladas' => $ventas->where('estado', 'Cancelada')->count(),
                    'monto_confirmadas' => $ventas->where('estado', 'Confirmada')->sum('monto_total'),
                    'monto_canceladas' => $ventas->where('estado', 'Cancelada')->sum('monto_total'),
                    'total' => $ventas->count(),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.ventas', compact('ventas', 'totales', 'filtros'));
                $nombreArchivo = 'reporte_ventas_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Ventas - BoA';
                break;

            case 'ingresos':
                $query = \App\Models\Ingreso::with(['programacionVuelo.vuelo', 'programacionVuelo.ruta.aeropuertoOrigen', 'programacionVuelo.ruta.aeropuertoDestino']);
                if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
                if ($request->filled('fecha_fin')) $query->whereDate('created_at', '<=', $request->fecha_fin);
                $ingresos = $query->orderBy('created_at', 'desc')->get();
                $totales = [
                    'monto_total' => $ingresos->sum('monto_total'),
                    'pasajes_total' => $ingresos->sum('cantidad_pasajes'),
                    'cantidad' => $ingresos->count(),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.ingresos', compact('ingresos', 'totales', 'filtros'));
                $nombreArchivo = 'reporte_ingresos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Ingresos - BoA';
                break;

            case 'egresos':
                $query = \App\Models\Egreso::with(['devolucion.venta.programacionVuelo.vuelo', 'devolucion.cliente']);
                if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
                if ($request->filled('fecha_fin')) $query->whereDate('created_at', '<=', $request->fecha_fin);
                $egresos = $query->orderBy('created_at', 'desc')->get();
                $totales = [
                    'monto_total' => $egresos->sum('monto_devuelto'),
                    'cantidad' => $egresos->count(),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.egresos', compact('egresos', 'totales', 'filtros'));
                $nombreArchivo = 'reporte_egresos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Egresos - BoA';
                break;
        }

        $pdf->setPaper('letter', 'landscape');
        $pdfContent = $pdf->output();

        Mail::raw("Adjunto encontrará el {$asunto} generado el " . date('d/m/Y H:i') . ".\n\nBoliviana de Aviación (BoA) - Sistema de Información Web", function ($message) use ($email, $asunto, $pdfContent, $nombreArchivo) {
            $message->to($email)
                    ->subject($asunto)
                    ->attachData($pdfContent, $nombreArchivo, ['mime' => 'application/pdf']);
        });

        return back()->with('success', "Reporte enviado exitosamente a {$email}");
    }
}