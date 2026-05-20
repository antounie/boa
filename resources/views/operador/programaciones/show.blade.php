@extends('layouts.app')

@section('titulo', 'Detalle Programación')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-calendar3"></i> Detalle de Programación: {{ $programacion->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Vuelo:</strong>
                        <p class="text-primary fs-5">{{ $programacion->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Ruta:</strong>
                        <p class="fs-5">
                            {{ $programacion->aeropuertoOrigen->codigo_IATA }}
                            <i class="bi bi-arrow-right"></i>
                            {{ $programacion->aeropuertoDestino->codigo_IATA }}
                        </p>
                        <small class="text-muted">
                            {{ $programacion->aeropuertoOrigen->ciudad }} → {{ $programacion->aeropuertoDestino->ciudad }}
                        </small>
                    </div>
                    <div class="col-md-3">
                        <strong>Aeronave:</strong>
                        <p>{{ $programacion->aeronave->matricula }}<br>
                        <small class="text-muted">{{ $programacion->aeronave->modelo }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Estado:</strong>
                        <p>
                            @if($programacion->estado === 'Programado')
                                <span class="badge bg-primary fs-6">Programado</span>
                            @elseif($programacion->estado === 'Completo')
                                <span class="badge bg-warning text-dark fs-6">Completo</span>
                            @else
                                <span class="badge bg-success fs-6">Salido</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Fecha Salida:</strong>
                        <p>{{ $programacion->fecha_salida }} - {{ $programacion->hora_salida }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Llegada:</strong>
                        <p>{{ $programacion->fecha_llegada }} - {{ $programacion->hora_llegada }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Precio Base:</strong>
                        <p class="fs-5 text-success">${{ number_format($programacion->precio_base, 2) }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Asientos Vendidos:</strong>
                        <p class="fs-5">{{ $programacion->asientos_vendidos }} / {{ $programacion->aeronave->capacidad_total }}</p>
                    </div>
                </div>

                {{-- Tripulación --}}
                @if($programacion->tripulacion->count() > 0)
                <hr>
                <h5><i class="bi bi-people"></i> Tripulación Asignada</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Cargo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programacion->tripulacion as $trip)
                        <tr>
                            <td>{{ $trip->empleado->nombre }} {{ $trip->empleado->apellido }}</td>
                            <td><span class="badge bg-secondary">{{ $trip->cargo }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-exclamation-triangle"></i> Sin tripulación asignada.
                </div>
                @endif

                @if($programacion->fecha_original)
                <hr>
                <div class="alert alert-warning">
                    <h6 class="fw-bold"><i class="bi bi-calendar-check me-2"></i>Vuelo Reprogramado</h6>
                    <div class="row g-2 small">
                        <div class="col-sm-4">
                            <strong>Fecha original:</strong><br>
                            {{ \Carbon\Carbon::parse($programacion->fecha_original)->format('d/m/Y') }} — {{ $programacion->hora_original }} hrs
                        </div>
                        <div class="col-sm-4">
                            <strong>Nueva fecha:</strong><br>
                            {{ \Carbon\Carbon::parse($programacion->fecha_salida)->format('d/m/Y') }} — {{ $programacion->hora_salida }} hrs
                        </div>
                        <div class="col-sm-4">
                            <strong>Motivo:</strong><br>
                            {{ $programacion->motivo_reprogramacion }}
                        </div>
                    </div>
                </div>
                @endif

                <hr>
                <div class="d-flex gap-2">
                    <a href="{{ route('operador.programaciones.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    @if($programacion->estado !== 'Salido')
                    <a href="{{ route('operador.programaciones.reprogramar', $programacion) }}" class="btn btn-warning">
                        <i class="bi bi-calendar-check me-1"></i> Reprogramar
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
