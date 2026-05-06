<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionVuelo;
use App\Models\Asiento;
use App\Models\Cliente;
use App\Models\Transaccion;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\Ticket;
use App\Services\LibelulaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AsientoProgramacion;


class PagoController extends Controller
{
    public function procesarPago(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id' => 'required|exists:asientos,id',
            'tipo' => 'required|in:reserva,compra',
        ]);

        $programacion = ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino', 'aeronave'])->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $asientoDisponible = AsientoProgramacion::where('asiento_id', $request->asiento_id)
            ->where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('estado', 'Disponible')
            ->exists();

        if (!$asientoDisponible) {
            return redirect()->route('cliente.buscar')->with('error', 'El asiento ya no está disponible.');
        }

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')->with('error', 'Este vuelo ya no está disponible.');
        }

        $monto = $programacion->precio_base * $asiento->tipoClase->multiplicador_precio;
        $identificador = 'BOA-' . strtoupper(Str::random(10));

        // Registrar deuda en Libélula
        $libelula = new LibelulaService();
        $resultado = $libelula->registrarDeuda([
            'email' => $cliente->email,
            'identificador' => $identificador,
            'descripcion' => "Pasaje {$programacion->vuelo->codigo_vuelo} {$programacion->ruta->aeropuertoOrigen->codigo_IATA}-{$programacion->ruta->aeropuertoDestino->codigo_IATA}",
            'nombre' => $cliente->nombre,
            'apellido' => $cliente->apellido,
            'monto' => $monto,
            'concepto' => "Pasaje aéreo {$programacion->vuelo->codigo_vuelo} - Asiento {$asiento->numero} ({$asiento->tipoClase->nombre})",
            'callback_url' => route('cliente.pago.callback', ['identificador' => $identificador]),
            'url_retorno' => route('cliente.pago.resultado', ['identificador' => $identificador]),
        ]);

        if (!$resultado['success']) {
            return redirect()->route('cliente.buscar')->with('error', 'Error al procesar el pago: ' . ($resultado['mensaje'] ?? 'Intente de nuevo.'));
        }

        // Guardar datos en sesión
        session([
            'pago_pendiente' => [
                'identificador' => $identificador,
                'programacion_vuelo_id' => $programacion->id,
                'asiento_id' => $asiento->id,
                'cliente_id' => $cliente->id,
                'monto' => $monto,
                'tipo' => $request->tipo,
                'libelula_id' => $resultado['id_transaccion'],
                'modo' => $resultado['modo'],
            ]
        ]);

        // Mostrar página de pago con QR
        return view('cliente.pago-qr', compact('programacion', 'asiento', 'cliente', 'monto', 'identificador', 'resultado'));
    }

    public function callback(Request $request, $identificador)
    {
        $pagoPendiente = session('pago_pendiente');

        if (!$pagoPendiente || $pagoPendiente['identificador'] !== $identificador) {
            return response()->json(['status' => 'error', 'mensaje' => 'Sesión no encontrada']);
        }

        // Libélula envía el callback cuando el pago es exitoso
        if ($request->has('transaction_id')) {
            return $this->completarPago($pagoPendiente);
        }

        return response()->json(['status' => 'pendiente']);
    }

    public function resultado(Request $request, $identificador)
    {
        $pagoPendiente = session('pago_pendiente');

        if (!$pagoPendiente || $pagoPendiente['identificador'] !== $identificador) {
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'error']);
            }
            return redirect()->route('cliente.dashboard')->with('info', 'Pago procesado anteriormente.');
        }

        // Si viene de Libélula con transaction_id en el callback
        if ($request->has('transaction_id')) {
            return $this->completarPago($pagoPendiente);
        }

        // Verificar con Libélula si el pago fue realizado
        $libelula = new LibelulaService();
        $verificacion = $libelula->verificarPago($pagoPendiente['libelula_id']);

        if ($verificacion['success'] && $verificacion['pagado']) {
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'completado']);
            }
            return $this->completarPago($pagoPendiente);
        }

        // Pago aún no confirmado
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json(['status' => 'pendiente']);
        }

        return redirect()->route('cliente.buscar')->with('info', 'El pago aún no ha sido confirmado.');
    }

    public function confirmarSimulacion(Request $request)
    {
        $pagoPendiente = session('pago_pendiente');

        if (!$pagoPendiente) {
            return redirect()->route('cliente.buscar')->with('error', 'No hay pago pendiente.');
        }

        // Verificar que no se haya procesado ya
        $yaExiste = Transaccion::where('referencia', $pagoPendiente['libelula_id'])->exists();
        if ($yaExiste) {
            session()->forget('pago_pendiente');
            return redirect()->route('cliente.mis.compras')->with('info', 'Este pago ya fue procesado.');
        }

        // Verificar con Libélula si realmente se pagó
        $libelula = new LibelulaService();
        $verificacion = $libelula->verificarPago($pagoPendiente['libelula_id']);

        if ($verificacion['success'] && $verificacion['pagado']) {
            return $this->completarPago($pagoPendiente);
        }

        // Si es modo simulación, permitir confirmar directamente
        if ($pagoPendiente['modo'] === 'simulacion') {
            return $this->completarPago($pagoPendiente);
        }

        return redirect()->back()->with('error', 'El pago aún no ha sido verificado. Por favor escanee el QR y realice el pago primero.');
    }

    private function completarPago($pagoPendiente)
    {
        // Verificar si ya se procesó este pago (evitar duplicados)
        $yaExiste = Transaccion::where('referencia', $pagoPendiente['libelula_id'])->exists();
        if ($yaExiste) {
            session()->forget('pago_pendiente');
            return redirect()->route('cliente.mis.compras')->with('info', 'Este pago ya fue procesado anteriormente.');
        }

        $programacion = ProgramacionVuelo::with('aeronave')->find($pagoPendiente['programacion_vuelo_id']);
        $asiento = Asiento::find($pagoPendiente['asiento_id']);
        $reserva = null;
        $venta = null;
        $ticket = null;

        try {
            DB::beginTransaction();

            $asientoProg = AsientoProgramacion::where('asiento_id', $asiento->id)
                ->where('programacion_vuelo_id', $programacion->id)
                ->where('estado', 'Disponible')
                ->lockForUpdate()
                ->first();

            if (!$asientoProg) {
                DB::rollBack();
                session()->forget('pago_pendiente');
                return redirect()->route('cliente.buscar')->with('error', 'El asiento fue reservado por otra persona.');
            }

            $transaccion = Transaccion::create([
                'referencia' => $pagoPendiente['libelula_id'],
                'monto' => $pagoPendiente['monto'],
                'metodo_pago' => 'QR',
                'estado' => 'Aprobado',
            ]);

            if ($pagoPendiente['tipo'] === 'reserva') {
                $reserva = Reserva::create([
                    'codigo_reserva' => 'RES-' . strtoupper(Str::random(8)),
                    'programacion_vuelo_id' => $programacion->id,
                    'cliente_id' => $pagoPendiente['cliente_id'],
                    'asiento_id' => $asiento->id,
                    'monto' => $pagoPendiente['monto'],
                    'estado' => 'Confirmada',
                ]);
            }

            $venta = Venta::create([
                'codigo_venta' => 'VTA-' . strtoupper(Str::random(8)),
                'programacion_vuelo_id' => $programacion->id,
                'cliente_id' => $pagoPendiente['cliente_id'],
                'asiento_id' => $asiento->id,
                'transaccion_id' => $transaccion->id,
                'reserva_id' => $reserva ? $reserva->id : null,
                'metodo_pago' => 'QR',
                'monto_total' => $pagoPendiente['monto'],
                'estado' => 'Confirmada',
            ]);

            $ticket = Ticket::create([
                'numero_ticket' => 'TKT-' . strtoupper(Str::random(8)),
                'venta_id' => $venta->id,
                'estado' => 'Emitido',
            ]);

            $asientoProg->update(['estado' => 'Ocupado']);
            $programacion->increment('asientos_vendidos');

            if ($programacion->asientos_vendidos >= $programacion->aeronave->capacidad_total) {
                $programacion->update(['estado' => 'Completo']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cliente.buscar')->with('error', 'Error al completar el pago. Contacte al soporte.');
        }

        session()->forget('pago_pendiente');

        $ruta = $pagoPendiente['tipo'] === 'reserva' ? 'cliente.mis.reservas' : 'cliente.mis.compras';
        $mensaje = $pagoPendiente['tipo'] === 'reserva'
            ? "Reserva confirmada exitosamente. Código: {$reserva->codigo_reserva} | Ticket: {$ticket->numero_ticket}"
            : "Compra realizada exitosamente. Código: {$venta->codigo_venta} | Ticket: {$ticket->numero_ticket}";

        return redirect()->route($ruta)->with('success', $mensaje);
    }
}