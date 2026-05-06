@extends('layouts.app')

@section('titulo', 'Detalle Devolución')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-arrow-return-left"></i> Detalle de Devolución #{{ $devolucion->id }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Cliente:</strong>
                        <p>{{ $devolucion->cliente->nombre }} {{ $devolucion->cliente->apellido }}
                        <br><small class="text-muted">{{ $devolucion->cliente->documento_identidad }}</small></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Devolución:</strong>
                        <p>{{ $devolucion->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Código Venta:</strong>
                        <p><strong>{{ $devolucion->venta->codigo_venta }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <strong>Vuelo:</strong>
                        <p>{{ $devolucion->venta->programacionVuelo->vuelo->codigo_vuelo }} |
                        {{ $devolucion->venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $devolucion->venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Asiento:</strong>
                        <p>{{ $devolucion->venta->asiento->numero }} ({{ $devolucion->venta->asiento->tipoClase->nombre }})</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body">
                                <h6>Monto Devuelto</h6>
                                <h3>${{ number_format($devolucion->monto_devolucion, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-warning text-dark text-center">
                            <div class="card-body">
                                <h6>Egreso Financiero</h6>
                                <h3>
                                    @if($devolucion->egreso)
                                        <i class="bi bi-check-circle"></i> Registrado
                                    @else
                                        <i class="bi bi-clock"></i> Pendiente
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <strong>Motivo:</strong>
                    <p class="border rounded p-3 bg-light">{{ $devolucion->motivo }}</p>
                </div>

                @if($devolucion->venta->ticket)
                <div class="mb-4">
                    <strong>Ticket:</strong>
                    <p>{{ $devolucion->venta->ticket->numero_ticket }} —
                        <span class="badge bg-danger">{{ $devolucion->venta->ticket->estado }}</span>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection