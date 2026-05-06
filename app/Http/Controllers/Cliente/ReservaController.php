<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\Transaccion;
use App\Models\Ticket;
use App\Models\Asiento;
use App\Models\AsientoProgramacion;
use App\Models\ProgramacionVuelo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    public function misReservas()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'No se encontró su perfil de cliente.');
        }

        $reservas = Reserva::with([
                'programacionVuelo.vuelo',
                'programacionVuelo.ruta.aeropuertoOrigen',
                'programacionVuelo.ruta.aeropuertoDestino',
                'asiento.tipoClase',
                'venta'
            ])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.mis-reservas', compact('reservas'));
    }

    public function misCompras()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'No se encontró su perfil de cliente.');
        }

        $ventas = Venta::with([
                'programacionVuelo.vuelo',
                'programacionVuelo.ruta.aeropuertoOrigen',
                'programacionVuelo.ruta.aeropuertoDestino',
                'asiento.tipoClase',
                'transaccion',
                'ticket'
            ])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.mis-compras', compact('ventas'));
    }

    public function misTickets()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'No se encontró su perfil de cliente.');
        }

        $tickets = Ticket::with([
                'venta.programacionVuelo.vuelo',
                'venta.programacionVuelo.ruta.aeropuertoOrigen',
                'venta.programacionVuelo.ruta.aeropuertoDestino',
                'venta.asiento.tipoClase'
            ])
            ->whereHas('venta', function ($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.mis-tickets', compact('tickets'));
    }

    public function confirmarReserva(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id' => 'required|exists:asientos,id',
        ]);

        $programacion = ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino'])->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $asientoDisponible = AsientoProgramacion::where('asiento_id', $request->asiento_id)
            ->where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('estado', 'Disponible')
            ->exists();

        if (!$asientoDisponible) {
            return back()->with('error', 'El asiento seleccionado ya no está disponible.');
        }

        return view('cliente.confirmar-reserva', compact('programacion', 'asiento', 'cliente'));
    }

    public function procesarReserva(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id' => 'required|exists:asientos,id',
            'metodo_pago' => 'required|in:Tarjeta,QR,Transferencia',
        ]);

        $programacion = ProgramacionVuelo::with('aeronave')->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'No se encontró su perfil de cliente.');
        }

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')
                ->with('error', 'Este vuelo ya no está disponible.');
        }

        try {
            $resultado = $this->crearVentaConTicket($programacion, $asiento, $cliente, $request->metodo_pago, true);
        } catch (\RuntimeException $e) {
            return redirect()->route('cliente.buscar')->with('error', $e->getMessage());
        }

        return redirect()->route('cliente.mis.reservas')
            ->with('success', "Reserva confirmada exitosamente. Código: {$resultado['reserva']->codigo_reserva} | Ticket: {$resultado['ticket']->numero_ticket}");
    }

    public function compraDirecta(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id' => 'required|exists:asientos,id',
        ]);

        $programacion = ProgramacionVuelo::with(['vuelo', 'ruta.aeropuertoOrigen', 'ruta.aeropuertoDestino'])->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $asientoDisponible = AsientoProgramacion::where('asiento_id', $request->asiento_id)
            ->where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('estado', 'Disponible')
            ->exists();

        if (!$asientoDisponible) {
            return back()->with('error', 'El asiento seleccionado ya no está disponible.');
        }

        return view('cliente.confirmar-compra', compact('programacion', 'asiento', 'cliente'));
    }

    public function procesarCompra(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id' => 'required|exists:asientos,id',
            'metodo_pago' => 'required|in:Tarjeta,QR,Transferencia',
        ]);

        $programacion = ProgramacionVuelo::with('aeronave')->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'No se encontró su perfil de cliente.');
        }

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')
                ->with('error', 'Este vuelo ya no está disponible.');
        }

        try {
            $resultado = $this->crearVentaConTicket($programacion, $asiento, $cliente, $request->metodo_pago, false);
        } catch (\RuntimeException $e) {
            return redirect()->route('cliente.buscar')->with('error', $e->getMessage());
        }

        return redirect()->route('cliente.mis.compras')
            ->with('success', "Compra realizada exitosamente. Código: {$resultado['venta']->codigo_venta} | Ticket: {$resultado['ticket']->numero_ticket}");
    }

    private function crearVentaConTicket(
        ProgramacionVuelo $programacion,
        Asiento $asiento,
        Cliente $cliente,
        string $metodoPago,
        bool $conReserva
    ): array {
        DB::beginTransaction();
        try {
            $asientoProg = AsientoProgramacion::where('asiento_id', $asiento->id)
                ->where('programacion_vuelo_id', $programacion->id)
                ->where('estado', 'Disponible')
                ->lockForUpdate()
                ->first();

            if (!$asientoProg) {
                throw new \RuntimeException('El asiento seleccionado ya no está disponible.');
            }

            $monto = $programacion->precio_base * $asiento->tipoClase->multiplicador_precio;

            $transaccion = Transaccion::create([
                'referencia' => 'TXN-' . strtoupper(Str::random(10)),
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'estado' => 'Aprobado',
            ]);

            $reserva = null;
            if ($conReserva) {
                $reserva = Reserva::create([
                    'codigo_reserva' => 'RES-' . strtoupper(Str::random(8)),
                    'programacion_vuelo_id' => $programacion->id,
                    'cliente_id' => $cliente->id,
                    'asiento_id' => $asiento->id,
                    'monto' => $monto,
                    'estado' => 'Confirmada',
                ]);
            }

            $venta = Venta::create([
                'codigo_venta' => 'VTA-' . strtoupper(Str::random(8)),
                'programacion_vuelo_id' => $programacion->id,
                'cliente_id' => $cliente->id,
                'asiento_id' => $asiento->id,
                'transaccion_id' => $transaccion->id,
                'reserva_id' => $reserva?->id,
                'metodo_pago' => $metodoPago,
                'monto_total' => $monto,
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
            return compact('reserva', 'venta', 'ticket');
        } catch (\RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException('Error al procesar. Intente de nuevo.');
        }
    }
}