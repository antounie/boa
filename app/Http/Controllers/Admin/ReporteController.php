<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Venta;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\Aeropuerto;
use App\Models\Aeronave;
use App\Models\TipoClase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
        if ($request->filled('aeropuerto_origen')) {
            $query->whereHas('ruta', fn($q) =>
                $q->where('aeropuerto_origen_id', $request->aeropuerto_origen)
            );
        }
        if ($request->filled('aeropuerto_destino')) {
            $query->whereHas('ruta', fn($q) =>
                $q->where('aeropuerto_destino_id', $request->aeropuerto_destino)
            );
        }
        if ($request->filled('aeronave_id')) {
            $query->where('aeronave_id', $request->aeronave_id);
        }

        $programaciones = $query->orderBy('fecha_salida', 'desc')->get();

        $totales = [
            'programados' => $programaciones->where('estado', 'Programado')->count(),
            'completos'   => $programaciones->where('estado', 'Completo')->count(),
            'salidos'     => $programaciones->where('estado', 'Salido')->count(),
            'total'       => $programaciones->count(),
            'vendidos'    => $programaciones->sum('asientos_vendidos'),
        ];

        // Datos para gráfico: vuelos por mes
        $chart = $programaciones
            ->groupBy(fn($p) => Carbon::parse($p->fecha_salida)->format('M Y'))
            ->map(fn($g) => $g->count())
            ->sortBy(fn($v, $k) => Carbon::parse($k)->timestamp);
        $chartLabels = $chart->keys()->values()->toJson();
        $chartData   = $chart->values()->toJson();

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();
        $aeronaves   = Aeronave::orderBy('matricula')->get();

        return view('admin.reportes.vuelos', compact(
            'programaciones', 'totales', 'aeropuertos', 'aeronaves', 'chartLabels', 'chartData'
        ));
    }

    public function vuelosPdf(Request $request)
    {
        $query = ProgramacionVuelo::with([
            'vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave'
        ]);
        $this->applyVuelosFiltros($query, $request);
        $programaciones = $query->orderBy('fecha_salida', 'desc')->get();

        $totales = [
            'programados' => $programaciones->where('estado', 'Programado')->count(),
            'completos'   => $programaciones->where('estado', 'Completo')->count(),
            'salidos'     => $programaciones->where('estado', 'Salido')->count(),
            'total'       => $programaciones->count(),
            'vendidos'    => $programaciones->sum('asientos_vendidos'),
        ];
        $filtros = $request->only(['fecha_inicio','fecha_fin','estado','aeropuerto_origen','aeropuerto_destino','aeronave_id']);

        $pdf = Pdf::loadView('admin.reportes.pdf.vuelos', compact('programaciones','totales','filtros'));
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
        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }
        if ($request->filled('clase_id')) {
            $query->whereHas('asiento', fn($q) =>
                $q->where('tipo_clase_id', $request->clase_id)
            );
        }
        if ($request->filled('aeropuerto_origen')) {
            $query->whereHas('programacionVuelo.ruta', fn($q) =>
                $q->where('aeropuerto_origen_id', $request->aeropuerto_origen)
            );
        }
        if ($request->filled('aeropuerto_destino')) {
            $query->whereHas('programacionVuelo.ruta', fn($q) =>
                $q->where('aeropuerto_destino_id', $request->aeropuerto_destino)
            );
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'confirmadas'      => $ventas->where('estado', 'Confirmada')->count(),
            'canceladas'       => $ventas->where('estado', 'Cancelada')->count(),
            'monto_confirmadas'=> $ventas->where('estado', 'Confirmada')->sum('monto_total'),
            'monto_canceladas' => $ventas->where('estado', 'Cancelada')->sum('monto_total'),
            'total'            => $ventas->count(),
        ];

        // Datos gráfico: monto confirmadas vs canceladas por mes
        $meses = $ventas->groupBy(fn($v) => $v->created_at->format('M Y'))
            ->sortBy(fn($g, $k) => Carbon::parse($k)->timestamp);
        $chartLabels    = $meses->keys()->values()->toJson();
        $chartConfirm   = $meses->map(fn($g) => $g->where('estado','Confirmada')->sum('monto_total'))->values()->toJson();
        $chartCancelada = $meses->map(fn($g) => $g->where('estado','Cancelada')->sum('monto_total'))->values()->toJson();

        $tipoClases  = TipoClase::orderBy('nombre')->get();
        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();
        $metodosPago = $ventas->pluck('metodo_pago')->unique()->filter()->sort()->values();

        return view('admin.reportes.ventas', compact(
            'ventas','totales','tipoClases','aeropuertos','metodosPago',
            'chartLabels','chartConfirm','chartCancelada'
        ));
    }

    public function ventasPdf(Request $request)
    {
        $query = Venta::with([
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino',
            'cliente', 'asiento.tipoClase'
        ]);
        $this->applyVentasFiltros($query, $request);
        $ventas = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'confirmadas'      => $ventas->where('estado','Confirmada')->count(),
            'canceladas'       => $ventas->where('estado','Cancelada')->count(),
            'monto_confirmadas'=> $ventas->where('estado','Confirmada')->sum('monto_total'),
            'monto_canceladas' => $ventas->where('estado','Cancelada')->sum('monto_total'),
            'total'            => $ventas->count(),
        ];
        $filtros = $request->only(['fecha_inicio','fecha_fin','estado','metodo_pago','clase_id','aeropuerto_origen','aeropuerto_destino']);

        $pdf = Pdf::loadView('admin.reportes.pdf.ventas', compact('ventas','totales','filtros'));
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
        if ($request->filled('aeropuerto_origen')) {
            $query->whereHas('programacionVuelo.ruta', fn($q) =>
                $q->where('aeropuerto_origen_id', $request->aeropuerto_origen)
            );
        }
        if ($request->filled('aeropuerto_destino')) {
            $query->whereHas('programacionVuelo.ruta', fn($q) =>
                $q->where('aeropuerto_destino_id', $request->aeropuerto_destino)
            );
        }

        $ingresos = $query->orderBy('created_at', 'desc')->get();

        $totales = [
            'monto_total'  => $ingresos->sum('monto_total'),
            'pasajes_total'=> $ingresos->sum('cantidad_pasajes'),
            'cantidad'     => $ingresos->count(),
            'promedio'     => $ingresos->count() > 0 ? $ingresos->avg('monto_total') : 0,
        ];

        // Datos gráfico: monto por mes
        $chart = $ingresos
            ->groupBy(fn($i) => $i->created_at->format('M Y'))
            ->sortBy(fn($g, $k) => Carbon::parse($k)->timestamp);
        $chartLabels = $chart->keys()->values()->toJson();
        $chartData   = $chart->map(fn($g) => $g->sum('monto_total'))->values()->toJson();

        $aeropuertos = Aeropuerto::orderBy('ciudad')->get();

        return view('admin.reportes.ingresos', compact(
            'ingresos','totales','aeropuertos','chartLabels','chartData'
        ));
    }

    public function ingresosPdf(Request $request)
    {
        $query = Ingreso::with([
            'programacionVuelo.vuelo',
            'programacionVuelo.ruta.aeropuertoOrigen',
            'programacionVuelo.ruta.aeropuertoDestino'
        ]);
        if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin'))    $query->whereDate('created_at', '<=', $request->fecha_fin);

        $ingresos = $query->orderBy('created_at', 'desc')->get();
        $totales = [
            'monto_total'  => $ingresos->sum('monto_total'),
            'pasajes_total'=> $ingresos->sum('cantidad_pasajes'),
            'cantidad'     => $ingresos->count(),
            'promedio'     => $ingresos->count() > 0 ? $ingresos->avg('monto_total') : 0,
        ];
        $filtros = $request->only(['fecha_inicio','fecha_fin','aeropuerto_origen','aeropuerto_destino']);

        $pdf = Pdf::loadView('admin.reportes.pdf.ingresos', compact('ingresos','totales','filtros'));
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
            'cantidad'    => $egresos->count(),
            'promedio'    => $egresos->count() > 0 ? $egresos->avg('monto_devuelto') : 0,
        ];

        // Datos gráfico: monto devuelto por mes
        $chart = $egresos
            ->groupBy(fn($e) => $e->created_at->format('M Y'))
            ->sortBy(fn($g, $k) => Carbon::parse($k)->timestamp);
        $chartLabels = $chart->keys()->values()->toJson();
        $chartData   = $chart->map(fn($g) => $g->sum('monto_devuelto'))->values()->toJson();

        return view('admin.reportes.egresos', compact('egresos','totales','chartLabels','chartData'));
    }

    public function egresosPdf(Request $request)
    {
        $query = Egreso::with([
            'devolucion.venta.programacionVuelo.vuelo',
            'devolucion.cliente'
        ]);
        if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin'))    $query->whereDate('created_at', '<=', $request->fecha_fin);

        $egresos = $query->orderBy('created_at', 'desc')->get();
        $totales = [
            'monto_total' => $egresos->sum('monto_devuelto'),
            'cantidad'    => $egresos->count(),
            'promedio'    => $egresos->count() > 0 ? $egresos->avg('monto_devuelto') : 0,
        ];
        $filtros = $request->only(['fecha_inicio','fecha_fin']);

        $pdf = Pdf::loadView('admin.reportes.pdf.egresos', compact('egresos','totales','filtros'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('reporte_egresos_' . date('Y-m-d_H-i') . '.pdf');
    }

    // ===== ENVIAR POR CORREO =====
    public function enviarPorCorreo(Request $request)
    {
        $request->validate([
            'tipo'  => 'required|in:vuelos,ventas,ingresos,egresos',
            'email' => 'required|email',
        ]);

        $tipo    = $request->tipo;
        $email   = $request->email;
        $filtros = $request->except(['tipo','email','_token']);

        switch ($tipo) {
            case 'vuelos':
                $query = ProgramacionVuelo::with(['vuelo','ruta.aeropuertoOrigen','ruta.aeropuertoDestino','aeronave']);
                $this->applyVuelosFiltros($query, $request);
                $programaciones = $query->orderBy('fecha_salida','desc')->get();
                $totales = [
                    'programados' => $programaciones->where('estado','Programado')->count(),
                    'completos'   => $programaciones->where('estado','Completo')->count(),
                    'salidos'     => $programaciones->where('estado','Salido')->count(),
                    'total'       => $programaciones->count(),
                    'vendidos'    => $programaciones->sum('asientos_vendidos'),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.vuelos', compact('programaciones','totales','filtros'));
                $nombreArchivo = 'reporte_vuelos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Vuelos - BoA';
                break;

            case 'ventas':
                $query = Venta::with(['programacionVuelo.vuelo','programacionVuelo.ruta.aeropuertoOrigen','programacionVuelo.ruta.aeropuertoDestino','cliente','asiento.tipoClase']);
                $this->applyVentasFiltros($query, $request);
                $ventas = $query->orderBy('created_at','desc')->get();
                $totales = [
                    'confirmadas'      => $ventas->where('estado','Confirmada')->count(),
                    'canceladas'       => $ventas->where('estado','Cancelada')->count(),
                    'monto_confirmadas'=> $ventas->where('estado','Confirmada')->sum('monto_total'),
                    'monto_canceladas' => $ventas->where('estado','Cancelada')->sum('monto_total'),
                    'total'            => $ventas->count(),
                ];
                $pdf = Pdf::loadView('admin.reportes.pdf.ventas', compact('ventas','totales','filtros'));
                $nombreArchivo = 'reporte_ventas_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Ventas - BoA';
                break;

            case 'ingresos':
                $query = Ingreso::with(['programacionVuelo.vuelo','programacionVuelo.ruta.aeropuertoOrigen','programacionVuelo.ruta.aeropuertoDestino']);
                if ($request->filled('fecha_inicio')) $query->whereDate('created_at','>=',$request->fecha_inicio);
                if ($request->filled('fecha_fin'))    $query->whereDate('created_at','<=',$request->fecha_fin);
                $ingresos = $query->orderBy('created_at','desc')->get();
                $totales  = ['monto_total'=>$ingresos->sum('monto_total'),'pasajes_total'=>$ingresos->sum('cantidad_pasajes'),'cantidad'=>$ingresos->count(),'promedio'=>$ingresos->avg('monto_total')];
                $pdf = Pdf::loadView('admin.reportes.pdf.ingresos', compact('ingresos','totales','filtros'));
                $nombreArchivo = 'reporte_ingresos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Ingresos - BoA';
                break;

            case 'egresos':
                $query = Egreso::with(['devolucion.venta.programacionVuelo.vuelo','devolucion.cliente']);
                if ($request->filled('fecha_inicio')) $query->whereDate('created_at','>=',$request->fecha_inicio);
                if ($request->filled('fecha_fin'))    $query->whereDate('created_at','<=',$request->fecha_fin);
                $egresos = $query->orderBy('created_at','desc')->get();
                $totales = ['monto_total'=>$egresos->sum('monto_devuelto'),'cantidad'=>$egresos->count(),'promedio'=>$egresos->avg('monto_devuelto')];
                $pdf = Pdf::loadView('admin.reportes.pdf.egresos', compact('egresos','totales','filtros'));
                $nombreArchivo = 'reporte_egresos_' . date('Y-m-d') . '.pdf';
                $asunto = 'Reporte de Egresos - BoA';
                break;
        }

        $pdf->setPaper('letter', 'landscape');
        $pdfContent = $pdf->output();

        Mail::raw(
            "Adjunto encontrará el {$asunto} generado el " . date('d/m/Y H:i') . ".\n\nBoliviana de Aviación (BoA)",
            function ($message) use ($email, $asunto, $pdfContent, $nombreArchivo) {
                $message->to($email)
                    ->subject($asunto)
                    ->attachData($pdfContent, $nombreArchivo, ['mime' => 'application/pdf']);
            }
        );

        return back()->with('success', "Reporte enviado exitosamente a {$email}");
    }

    // ===== HELPERS PRIVADOS =====
    private function applyVuelosFiltros($query, Request $request): void
    {
        if ($request->filled('fecha_inicio'))      $query->whereDate('fecha_salida', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin'))         $query->whereDate('fecha_salida', '<=', $request->fecha_fin);
        if ($request->filled('estado'))            $query->where('estado', $request->estado);
        if ($request->filled('aeropuerto_origen')) $query->whereHas('ruta', fn($q) => $q->where('aeropuerto_origen_id', $request->aeropuerto_origen));
        if ($request->filled('aeropuerto_destino'))$query->whereHas('ruta', fn($q) => $q->where('aeropuerto_destino_id', $request->aeropuerto_destino));
        if ($request->filled('aeronave_id'))       $query->where('aeronave_id', $request->aeronave_id);
    }

    private function applyVentasFiltros($query, Request $request): void
    {
        if ($request->filled('fecha_inicio'))       $query->whereDate('created_at', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin'))          $query->whereDate('created_at', '<=', $request->fecha_fin);
        if ($request->filled('estado'))             $query->where('estado', $request->estado);
        if ($request->filled('metodo_pago'))        $query->where('metodo_pago', $request->metodo_pago);
        if ($request->filled('clase_id'))           $query->whereHas('asiento', fn($q) => $q->where('tipo_clase_id', $request->clase_id));
        if ($request->filled('aeropuerto_origen'))  $query->whereHas('programacionVuelo.ruta', fn($q) => $q->where('aeropuerto_origen_id', $request->aeropuerto_origen));
        if ($request->filled('aeropuerto_destino')) $query->whereHas('programacionVuelo.ruta', fn($q) => $q->where('aeropuerto_destino_id', $request->aeropuerto_destino));
    }
}
