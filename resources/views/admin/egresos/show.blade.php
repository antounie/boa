@extends('layouts.app')

@section('titulo', 'Detalle Egreso')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-graph-down-arrow"></i> Detalle de Egreso #{{ $egreso->id }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Cliente:</strong>
                        <p>{{ $egreso->devolucion->cliente->nombre }} {{ $egreso->devolucion->cliente->apellido }}
                        <br><small class="text-muted">{{ $egreso->devolucion->cliente->documento_identidad }}</small></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Código Venta:</strong>
                        <p><strong>{{ $egreso->devolucion->venta->codigo_venta }}</strong></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Vuelo:</strong>
                        <p>{{ $egreso->devolucion->venta->programacionVuelo->vuelo->codigo_vuelo }} |
                        {{ $egreso->devolucion->venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $egreso->devolucion->venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Asiento:</strong>
                        <p>{{ $egreso->devolucion->venta->asiento->numero }} ({{ $egreso->devolucion->venta->asiento->tipoClase->nombre }})</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha Egreso:</strong>
                        <p>{{ $egreso->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body">
                                <h6>Monto Devuelto</h6>
                                <h3>${{ number_format($egreso->monto_devuelto, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-warning text-dark text-center">
                            <div class="card-body">
                                <h6>Devolución</h6>
                                <h5>#{{ $egreso->devolucion->id }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <strong>Motivo de la Devolución:</strong>
                    <p class="border rounded p-3 bg-light">{{ $egreso->devolucion->motivo }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.egresos.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection