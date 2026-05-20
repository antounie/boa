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
    public function datosCompra(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_ids'           => 'required|array|min:1|max:10',
            'asiento_ids.*'         => 'required|exists:asientos,id',
            'sub_tramo_id'          => 'nullable|exists:tramos,id',
        ]);

        $programacion = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'precios'])
            ->find($request->programacion_vuelo_id);

        $subTramo = $request->filled('sub_tramo_id')
            ? \App\Models\Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])->find($request->sub_tramo_id)
            : null;

        $cliente  = Cliente::where('usuario_id', auth()->id())->first();
        $asientos = Asiento::with('tipoClase')->whereIn('id', $request->asiento_ids)->get();

        return view('cliente.datos-pasajeros', compact('programacion', 'asientos', 'cliente', 'subTramo'));
    }

    public function procesarPago(Request $request)
    {
        $request->validate([
            'programacion_vuelo_id' => 'required|exists:programacion_vuelos,id',
            'asiento_ids'           => 'required|array|min:1|max:10',
            'asiento_ids.*'         => 'required|exists:asientos,id',
            'sub_tramo_id'          => 'nullable|exists:tramos,id',
            'pasajeros'             => 'required|array',
            'pasajeros.*.nombre'    => 'required|string|max:80',
            'pasajeros.*.apellido'  => 'required|string|max:80',
        ]);

        $programacion = ProgramacionVuelo::with(['aeropuertoOrigen', 'aeropuertoDestino', 'aeronave', 'precios'])
            ->find($request->programacion_vuelo_id);

        $subTramo = $request->filled('sub_tramo_id')
            ? \App\Models\Tramo::with(['aeropuertoOrigen', 'aeropuertoDestino'])->find($request->sub_tramo_id)
            : null;

        if ($programacion->estado !== 'Programado') {
            return redirect()->route('cliente.buscar')->with('error', 'Este vuelo ya no está disponible.');
        }

        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        if (!$cliente) {
            return redirect()->route('cliente.dashboard')->with('error', 'No se encontró su perfil de cliente.');
        }

        $asientos = Asiento::with('tipoClase')->whereIn('id', $request->asiento_ids)->get();

        // Verificar disponibilidad de cada asiento
        $noDisponibles = [];
        foreach ($asientos as $asiento) {
            $ok = AsientoProgramacion::where('asiento_id', $asiento->id)
                ->where('programacion_vuelo_id', $programacion->id)
                ->where('estado', 'Disponible')
                ->exists();
            if (!$ok) {
                $noDisponibles[] = $asiento->numero;
            }
        }

        if (!empty($noDisponibles)) {
            return back()->with('error', 'Los siguientes asientos ya no están disponibles: ' . implode(', ', $noDisponibles));
        }

        // Calcular monto total usando precios por clase configurados
        $monto = $asientos->sum(function ($a) use ($programacion) {
            $precioPorClase = $programacion->precios->firstWhere('tipo_clase_id', $a->tipo_clase_id);
            return $precioPorClase
                ? (float) $precioPorClase->precio
                : $programacion->precio_base * $a->tipoClase->multiplicador_precio;
        });

        $identificador = 'BOA-' . strtoupper(Str::random(10));
        $concepto = $asientos->map(fn($a) => "Asiento {$a->numero} ({$a->tipoClase->nombre})")->implode(', ');

        $libelula = new LibelulaService();
        $resultado = $libelula->registrarDeuda([
            'email'        => $cliente->email,
            'identificador'=> $identificador,
            'descripcion'  => "Pasaje {$programacion->codigo_vuelo} {$programacion->aeropuertoOrigen->codigo_IATA}-{$programacion->aeropuertoDestino->codigo_IATA}",
            'nombre'       => $cliente->nombre,
            'apellido'     => $cliente->apellido,
            'monto'        => $monto,
            'concepto'     => "Pasaje aéreo {$programacion->codigo_vuelo} - {$concepto}",
            'callback_url' => route('cliente.pago.callback', ['identificador' => $identificador]),
            'url_retorno'  => route('cliente.pago.resultado', ['identificador' => $identificador]),
        ]);

        if (!$resultado['success']) {
            return redirect()->route('cliente.buscar')->with('error', 'Error al procesar el pago: ' . ($resultado['mensaje'] ?? 'Intente de nuevo.'));
        }

        session([
            'pago_pendiente' => [
                'identificador'         => $identificador,
                'programacion_vuelo_id' => $programacion->id,
                'asiento_ids'           => $request->asiento_ids,
                'sub_tramo_id'          => $request->sub_tramo_id,
                'cliente_id'            => $cliente->id,
                'monto'                 => $monto,
                'libelula_id'           => $resultado['id_transaccion'],
                'modo'                  => $resultado['modo'],
                'pasajeros'             => $request->pasajeros,
            ]
        ]);

        $pasajeros = $request->pasajeros;
        return view('cliente.pago-qr', compact('programacion', 'asientos', 'cliente', 'monto', 'identificador', 'resultado', 'subTramo', 'pasajeros'));
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
        $yaExiste = Transaccion::where('referencia', $pagoPendiente['libelula_id'])->exists();
        if ($yaExiste) {
            session()->forget('pago_pendiente');
            return redirect()->route('cliente.mis.compras')->with('info', 'Este pago ya fue procesado anteriormente.');
        }

        // Compatibilidad con sesiones antiguas (asiento_id singular)
        $asientoIds = $pagoPendiente['asiento_ids'] ?? [$pagoPendiente['asiento_id']];

        $programacion = ProgramacionVuelo::with('aeronave')->find($pagoPendiente['programacion_vuelo_id']);
        $asientos = Asiento::with('tipoClase')->whereIn('id', $asientoIds)->get()->keyBy('id');

        $ventas  = [];
        $tickets = [];

        try {
            DB::beginTransaction();

            // Bloquear y verificar todos los asientos a la vez
            $asientoProgs = AsientoProgramacion::whereIn('asiento_id', $asientoIds)
                ->where('programacion_vuelo_id', $programacion->id)
                ->where('estado', 'Disponible')
                ->lockForUpdate()
                ->get()
                ->keyBy('asiento_id');

            $noDisponibles = collect($asientoIds)->filter(fn($id) => !isset($asientoProgs[$id]));
            if ($noDisponibles->isNotEmpty()) {
                DB::rollBack();
                session()->forget('pago_pendiente');
                $numeros = $asientos->only($noDisponibles->values()->all())->pluck('numero')->implode(', ');
                return redirect()->route('cliente.buscar')
                    ->with('error', "Los asientos {$numeros} ya fueron tomados por otra persona.");
            }

            $venta = Venta::create([
                'codigo_venta'          => 'VTA-' . strtoupper(Str::random(8)),
                'programacion_vuelo_id' => $programacion->id,
                'cliente_id'            => $pagoPendiente['cliente_id'],
                'reserva_id'            => null,
                'monto_total'           => $pagoPendiente['monto'],
                'estado'                => 'Confirmada',
            ]);

            Transaccion::create([
                'venta_id'   => $venta->id,
                'referencia' => $pagoPendiente['libelula_id'],
                'monto'      => $pagoPendiente['monto'],
                'metodo_pago'=> 'QR',
                'estado'     => 'Aprobado',
            ]);

            $pasajeros = $pagoPendiente['pasajeros'] ?? [];
            foreach ($asientoIds as $asientoId) {
                $ticket = Ticket::create([
                    'numero_ticket'    => 'TKT-' . strtoupper(Str::random(8)),
                    'venta_id'         => $venta->id,
                    'asiento_id'       => $asientoId,
                    'sub_tramo_id'     => $pagoPendiente['sub_tramo_id'] ?? null,
                    'estado'           => 'Emitido',
                    'pasajero_nombre'  => $pasajeros[$asientoId]['nombre'] ?? null,
                    'pasajero_apellido'=> $pasajeros[$asientoId]['apellido'] ?? null,
                ]);

                $asientoProgs[$asientoId]->update(['estado' => 'Ocupado']);

                $tickets[] = $ticket;
            }

            $ventas[] = $venta;

            $cantidad = count($asientoIds);
            $programacion->increment('asientos_vendidos', $cantidad);
            $programacion->refresh();

            if ($programacion->asientos_vendidos >= $programacion->aeronave->capacidad_total) {
                $programacion->update(['estado' => 'Completo']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cliente.buscar')->with('error', 'Error al completar el pago. Contacte al soporte.');
        }

        session()->forget('pago_pendiente');

        $numTickets  = count($tickets);
        $codigosVenta = collect($ventas)->pluck('codigo_venta')->implode(', ');
        $mensaje = "{$numTickets} ticket(s) emitidos exitosamente. Códigos de venta: {$codigosVenta}";

        return redirect()->route('cliente.mis.compras')->with('success', $mensaje);
    }
}