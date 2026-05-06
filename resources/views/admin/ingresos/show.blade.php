@extends('layouts.app')

@section('titulo', 'Detalle Ingreso')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Detalle de Ingreso #{{ $ingreso->id }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Vuelo:</strong>
                        <p class="text-primary fs-5">{{ $ingreso->programacionVuelo->vuelo->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Ruta:</strong>
                        <p>{{ $ingreso->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ingreso->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}
                        <br><small class="text-muted">{{ $ingreso->programacionVuelo->ruta->aeropuertoOrigen->ciudad }} → {{ $ingreso->programacionVuelo->ruta->aeropuertoDestino->ciudad }}</small></p>
                    </div>
                    <div class="col-md-4">
                        <strong>Aeronave:</strong>
                        <p>{{ $ingreso->programacionVuelo->aeronave->matricula }}
                        <br><small class="text-muted">{{ $ingreso->programacionVuelo->aeronave->modelo }}</small></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>Monto Total</h6>
                                <h3>${{ number_format($ingreso->monto_total, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h6>Pasajes Vendidos</h6>
                                <h3>{{ $ingreso->cantidad_pasajes }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>Fecha Registro</h6>
                                <h5>{{ $ingreso->created_at->format('d/m/Y H:i') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Fecha Salida del Vuelo:</strong>
                        <p>{{ $ingreso->programacionVuelo->fecha_salida }} {{ $ingreso->programacionVuelo->hora_salida }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Generado desde Salida:</strong>
                        <p>#{{ $ingreso->salida->id }} — {{ $ingreso->salida->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.ingresos.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection