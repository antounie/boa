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
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                'programacionVuelo.aeropuertoOrigen',
                'programacionVuelo.aeropuertoDestino',
                'asiento.tipoClase',
                'venta',
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
                'programacionVuelo.aeropuertoOrigen',
                'programacionVuelo.aeropuertoDestino',
                'tickets.asiento.tipoClase',
                'transacciones',
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
                'asiento.tipoClase',
                'venta.programacionVuelo.aeropuertoOrigen',
                'venta.programacionVuelo.aeropuertoDestino',
                'subTramo.aeropuertoOrigen',
                'subTramo.aeropuertoDestino',
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
            'asiento_id'            => 'required|exists:asientos,id',
            'sub_tramo_id'          => 'nullable|exists:tramos,id',
        ]);

        $programacion = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'precios'])->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $subTramo = $request->filled('sub_tramo_id')
            ? \App\Models\Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])->find($request->sub_tramo_id)
            : null;

        $asientoDisponible = AsientoProgramacion::where('asiento_id', $request->asiento_id)
            ->where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('estado', 'Disponible')
            ->exists();

        if (!$asientoDisponible) {
            return back()->with('error', 'El asiento seleccionado ya no está disponible.');
        }

        return view('cliente.confirmar-reserva', compact('programacion', 'asiento', 'cliente', 'subTramo'));
    }

    public function procesarReserva(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id'            => 'required|exists:asientos,id',
            'metodo_pago'           => 'required|in:Tarjeta,QR,Transferencia',
            'pasajero_nombre'       => 'required|string|max:80',
            'pasajero_apellido'     => 'required|string|max:80',
        ]);

        $programacion = ProgramacionVuelo::with('aeronave')->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')->with('error', 'No se encontró su perfil de cliente.');
        }

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')->with('error', 'Este vuelo ya no está disponible.');
        }

        try {
            $resultado = $this->crearVentaConTicket(
                $programacion, $asiento, $cliente,
                $request->metodo_pago, true,
                $request->input('sub_tramo_id'),
                $request->pasajero_nombre,
                $request->pasajero_apellido
            );
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
            'asiento_id'            => 'required|exists:asientos,id',
            'sub_tramo_id'          => 'nullable|exists:tramos,id',
        ]);

        $programacion = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'precios'])->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $subTramo = $request->filled('sub_tramo_id')
            ? \App\Models\Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])->find($request->sub_tramo_id)
            : null;

        $asientoDisponible = AsientoProgramacion::where('asiento_id', $request->asiento_id)
            ->where('programacion_vuelo_id', $request->programacion_vuelo_id)
            ->where('estado', 'Disponible')
            ->exists();

        if (!$asientoDisponible) {
            return back()->with('error', 'El asiento seleccionado ya no está disponible.');
        }

        return view('cliente.confirmar-compra', compact('programacion', 'asiento', 'cliente', 'subTramo'));
    }

    public function procesarCompra(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_id'            => 'required|exists:asientos,id',
            'metodo_pago'           => 'required|in:Tarjeta,QR,Transferencia',
            'sub_tramo_id'          => 'nullable|exists:tramos,id',
            'pasajero_nombre'       => 'required|string|max:80',
            'pasajero_apellido'     => 'required|string|max:80',
        ]);

        $programacion = ProgramacionVuelo::with('aeronave')->find($request->programacion_vuelo_id);
        $asiento = Asiento::with('tipoClase')->find($request->asiento_id);
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')->with('error', 'No se encontró su perfil de cliente.');
        }

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')->with('error', 'Este vuelo ya no está disponible.');
        }

        try {
            $resultado = $this->crearVentaConTicket(
                $programacion, $asiento, $cliente,
                $request->metodo_pago, false,
                $request->input('sub_tramo_id'),
                $request->pasajero_nombre,
                $request->pasajero_apellido
            );
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
        bool $conReserva,
        ?int $subTramoId = null,
        ?string $pasajeroNombre = null,
        ?string $pasajeroApellido = null
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

            $programacion->loadMissing('precios');
            $precioPorClase = $programacion->precios->firstWhere('tipo_clase_id', $asiento->tipo_clase_id);
            $monto = $precioPorClase
                ? (float) $precioPorClase->precio
                : $programacion->precio_base * $asiento->tipoClase->multiplicador_precio;

            $reserva = null;
            if ($conReserva) {
                $reserva = Reserva::create([
                    'codigo_reserva'        => 'RES-' . strtoupper(Str::random(8)),
                    'programacion_vuelo_id' => $programacion->id,
                    'cliente_id'            => $cliente->id,
                    'asiento_id'            => $asiento->id,
                    'monto'                 => $monto,
                    'estado'                => 'Confirmada',
                ]);
            }

            $venta = Venta::create([
                'codigo_venta'          => 'VTA-' . strtoupper(Str::random(8)),
                'programacion_vuelo_id' => $programacion->id,
                'cliente_id'            => $cliente->id,
                'reserva_id'            => $reserva?->id,
                'monto_total'           => $monto,
                'estado'                => 'Confirmada',
            ]);

            Transaccion::create([
                'venta_id'   => $venta->id,
                'referencia' => 'TXN-' . strtoupper(Str::random(10)),
                'monto'      => $monto,
                'metodo_pago'=> $metodoPago,
                'estado'     => 'Aprobado',
            ]);

            $ticket = Ticket::create([
                'numero_ticket'    => 'TKT-' . strtoupper(Str::random(8)),
                'venta_id'         => $venta->id,
                'asiento_id'       => $asiento->id,
                'sub_tramo_id'     => $subTramoId,
                'estado'           => 'Emitido',
                'pasajero_nombre'  => $pasajeroNombre,
                'pasajero_apellido'=> $pasajeroApellido,
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

    public function downloadTicketPdf(Ticket $ticket)
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente || $ticket->venta->cliente_id !== $cliente->id) {
            abort(403);
        }

        $ticket->load([
            'asiento.tipoClase',
            'venta.programacionVuelo.aeropuertoOrigen',
            'venta.programacionVuelo.aeropuertoDestino',
            'venta.programacionVuelo.aeronave',
            'venta.cliente',
            'subTramo.aeropuertoOrigen',
            'subTramo.aeropuertoDestino',
        ]);

        $qrUrl  = route('ticket.verificar', $ticket->numero_ticket);
        $qrCode = base64_encode((string) QrCode::format('svg')->size(160)->errorCorrection('H')->generate($qrUrl));

        $pdf = Pdf::loadView('cliente.ticket-pdf', compact('ticket', 'qrCode'))
            ->setPaper([0, 0, 595, 290])
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        return $pdf->download("boarding-pass-{$ticket->numero_ticket}.pdf");
    }

    public function verificarTicket($numero)
    {
        $ticket = Ticket::with([
            'asiento.tipoClase',
            'venta.programacionVuelo.aeropuertoOrigen',
            'venta.programacionVuelo.aeropuertoDestino',
            'venta.cliente',
            'subTramo.aeropuertoOrigen',
            'subTramo.aeropuertoDestino',
        ])->where('numero_ticket', $numero)->first();

        if (!$ticket) {
            abort(404);
        }

        return view('cliente.verificar-ticket', compact('ticket'));
    }
}
