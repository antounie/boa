@extends('layouts.app')

@section('titulo', 'Detalle Venta')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-{{ $venta->estado === 'Confirmada' ? 'success' : 'danger' }} text-white">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Detalle de Venta: {{ $venta->codigo_venta }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Código Venta:</strong>
                        <p class="fs-5 fw-bold">{{ $venta->codigo_venta }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Estado:</strong>
                        <p>
                            @if($venta->estado === 'Confirmada')
                                <span class="badge bg-success fs-6">Confirmada</span>
                            @else
                                <span class="badge bg-danger fs-6">Cancelada</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Venta:</strong>
                        <p>{{ $venta->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Origen:</strong>
                        <p>
                            @if($venta->reserva_id)
                                <span class="badge bg-info">Desde Reserva</span>
                                <br><small>{{ $venta->reserva->codigo_reserva }}</small>
                            @else
                                <span class="badge bg-primary">Compra Directa</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>
                <h6><i class="bi bi-person"></i> Datos del Cliente</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Nombre:</strong>
                        <p>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Documento:</strong>
                        <p>{{ $venta->cliente->documento_identidad }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Email:</strong>
                        <p>{{ $venta->cliente->email }}</p>
                    </div>
                </div>

                <hr>
                <h6><i class="bi bi-airplane"></i> Datos del Vuelo</h6>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Vuelo:</strong>
                        <p class="text-primary fw-bold">{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Ruta:</strong>
                        <p>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}
                        <br><small class="text-muted">{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->ciudad }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Vuelo:</strong>
                        <p>{{ $venta->programacionVuelo->fecha_salida }}
                        <br>{{ $venta->programacionVuelo->hora_salida }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Aeronave:</strong>
                        <p>{{ $venta->programacionVuelo->aeronave->matricula }}
                        <br><small class="text-muted">{{ $venta->programacionVuelo->aeronave->modelo }}</small></p>
                    </div>
                </div>

                <hr>
                <h6><i class="bi bi-credit-card"></i> Datos del Pago</h6>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Asiento:</strong>
                        <p>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Método de Pago:</strong>
                        <p><span class="badge bg-info fs-6">{{ $venta->metodo_pago }}</span></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Transacción:</strong>
                        <p>{{ $venta->transaccion->referencia }}
                        <br><span class="badge bg-{{ $venta->transaccion->estado === 'Aprobado' ? 'success' : 'danger' }}">{{ $venta->transaccion->estado }}</span></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Monto Total:</strong>
                        <p class="fs-4 fw-bold text-success">${{ number_format($venta->monto_total, 2) }}</p>
                    </div>
                </div>

                @if($venta->ticket)
                <hr>
                <h6><i class="bi bi-ticket-perforated"></i> Ticket</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Número de Ticket:</strong>
                        <p class="fs-5">{{ $venta->ticket->numero_ticket }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong>
                        <p>
                            <span class="badge bg-{{ $venta->ticket->estado === 'Emitido' ? 'success' : 'danger' }} fs-6">
                                {{ $venta->ticket->estado }}
                            </span>
                        </p>
                    </div>
                </div>
                @endif

                @if($venta->devolucion)
                <hr>
                <h6><i class="bi bi-arrow-return-left"></i> Devolución</h6>
                <div class="alert alert-danger">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Monto Devuelto:</strong>
                            <p class="fs-5 fw-bold">${{ number_format($venta->devolucion->monto_devolucion, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Motivo:</strong>
                            <p>{{ $venta->devolucion->motivo }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Egreso:</strong>
                            <p>
                                @if($venta->devolucion->egreso)
                                    <span class="badge bg-success">Generado - ${{ number_format($venta->devolucion->egreso->monto_devuelto, 2) }}</span>
                                @else
                                    <span class="badge bg-warning">Pendiente</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection