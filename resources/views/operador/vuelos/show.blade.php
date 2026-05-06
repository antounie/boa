@extends('layouts.app')

@section('titulo', 'Detalle Vuelo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-airplane"></i> Detalle del Vuelo: {{ $vuelo->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Código:</strong>
                        <p class="text-primary fs-5">{{ $vuelo->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo:</strong>
                        <p>
                            @if($vuelo->tipo === 'Directo')
                                <span class="badge bg-primary fs-6">Directo</span>
                            @else
                                <span class="badge bg-warning text-dark fs-6">Con Escalas</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <strong>Estado:</strong>
                        <p>
                            @if($vuelo->estado === 'Activo')
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Activo</span>
                            @else
                                <span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Cancelado</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($vuelo->escalas->count() > 0)
                <hr>
                <h5><i class="bi bi-diagram-3"></i> Tramos de Escala ({{ $vuelo->escalas->count() }})</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Código Tramo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vuelo->escalas as $escala)
                        <tr>
                            <td><strong>{{ $escala->codigo_vuelo }}</strong></td>
                            <td>{{ $escala->tipo }}</td>
                            <td>
                                <span class="badge bg-{{ $escala->estado === 'Activo' ? 'success' : 'danger' }}">
                                    {{ $escala->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @if($vuelo->programaciones->count() > 0)
                <hr>
                <h5><i class="bi bi-calendar3"></i> Programaciones ({{ $vuelo->programaciones->count() }})</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Ruta</th>
                            <th>Fecha Salida</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vuelo->programaciones as $prog)
                        <tr>
                            <td>{{ $prog->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $prog->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $prog->fecha_salida }}</td>
                            <td>{{ $prog->hora_salida }}</td>
                            <td>
                                <span class="badge bg-{{ $prog->estado === 'Programado' ? 'primary' : ($prog->estado === 'Completo' ? 'warning' : 'success') }}">
                                    {{ $prog->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <hr>
                <a href="{{ route('operador.vuelos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection