@extends('layouts.app')

@section('titulo', 'Detalle Tramo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-diagram-3"></i>
                    Tramo: {{ $tramo->aeropuertoOrigen->codigo_IATA }} → {{ $tramo->aeropuertoDestino->codigo_IATA }}
                    @if($tramo->subTramos->count() > 0)
                        <span class="badge bg-warning text-dark ms-2">Con Escalas</span>
                    @else
                        <span class="badge bg-success ms-2">Directo</span>
                    @endif
                </h5>
            </div>
            <div class="card-body p-4">

                {{-- Datos principales --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Origen:</strong>
                        <p class="fs-5">{{ $tramo->aeropuertoOrigen->codigo_IATA }}
                        <br><small class="text-muted">{{ $tramo->aeropuertoOrigen->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Destino:</strong>
                        <p class="fs-5">{{ $tramo->aeropuertoDestino->codigo_IATA }}
                        <br><small class="text-muted">{{ $tramo->aeropuertoDestino->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Duración:</strong>
                        <p class="fs-5">{{ $tramo->duracion_estimada }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Tramo Padre:</strong>
                        @if($tramo->tramoPadre)
                            <p>{{ $tramo->tramoPadre->aeropuertoOrigen->codigo_IATA }} → {{ $tramo->tramoPadre->aeropuertoDestino->codigo_IATA }}</p>
                        @else
                            <p><span class="badge bg-primary">Tramo Raíz</span></p>
                        @endif
                    </div>
                </div>

                {{-- Sub-tramos (escalas) --}}
                @if($tramo->subTramos->count() > 0)
                <hr>
                <h6><i class="bi bi-diagram-3"></i> Sub-tramos (Escalas)</h6>
                <table class="table table-bordered table-sm mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>Orden</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Vuelo</th>
                            <th>Escala en destino</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tramo->subTramos->sortBy('orden') as $sub)
                        <tr>
                            <td class="text-center"><span class="badge bg-secondary">{{ $sub->orden }}</span></td>
                            <td><strong>{{ $sub->aeropuertoOrigen->codigo_IATA }}</strong> — {{ $sub->aeropuertoOrigen->ciudad }}</td>
                            <td><strong>{{ $sub->aeropuertoDestino->codigo_IATA }}</strong> — {{ $sub->aeropuertoDestino->ciudad }}</td>
                            <td>{{ $sub->duracion_estimada }}</td>
                            <td>
                                @if($sub->tiempo_escala)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock"></i> {{ $sub->tiempo_escala }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.tramos.edit', $sub) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('operador.tramos.create') }}?padre={{ $tramo->id }}" class="btn btn-sm btn-outline-primary mb-3">
                    <i class="bi bi-plus-lg"></i> Agregar Sub-tramo
                </a>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Este es un tramo directo sin escalas.
                    <a href="{{ route('operador.tramos.create') }}?padre={{ $tramo->id }}" class="alert-link ms-2">
                        Agregar sub-tramo de escala
                    </a>
                </div>
                @endif

                {{-- Rutas que usan este tramo --}}
                @if($tramo->rutas->count() > 0)
                <hr>
                <h6><i class="bi bi-signpost-2"></i> Rutas que usan este tramo</h6>
                <ul class="list-group mb-4">
                    @foreach($tramo->rutas as $ruta)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ruta->aeropuertoDestino->codigo_IATA }}
                        <span class="badge bg-{{ $ruta->tipo === 'Nacional' ? 'primary' : 'success' }}">{{ $ruta->tipo }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif

                <hr>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('operador.tramos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    <a href="{{ route('operador.tramos.edit', $tramo) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
