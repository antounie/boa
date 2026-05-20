@extends('layouts.app')

@section('titulo', 'Detalle Ruta')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-10">

        {{-- Datos de la Ruta --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-signpost-2"></i>
                    Ruta: {{ $ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ruta->aeropuertoDestino->codigo_IATA }}
                </h5>
                <span class="badge bg-{{ $ruta->tipo === 'Nacional' ? 'success' : 'primary' }} fs-6">{{ $ruta->tipo }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Origen:</strong>
                        <p class="fs-5">{{ $ruta->aeropuertoOrigen->codigo_IATA }}
                        <br><small class="text-muted">{{ $ruta->aeropuertoOrigen->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Destino:</strong>
                        <p class="fs-5">{{ $ruta->aeropuertoDestino->codigo_IATA }}
                        <br><small class="text-muted">{{ $ruta->aeropuertoDestino->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Distancia:</strong>
                        <p>{{ number_format($ruta->distancia, 0) }} km</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Duración Total:</strong>
                        <p>{{ $ruta->duracion_estimada }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tramos asignados a esta Ruta --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-diagram-3"></i> Tramos de esta Ruta</h6>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($ruta->tramos->count() > 0)
                <table class="table table-bordered align-middle mb-4">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">Orden</th>
                            <th>Origen</th>
                            <th></th>
                            <th>Destino</th>
                            <th>Duración</th>
                            <th>Escalas internas</th>
                            <th class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ruta->tramos->sortBy('pivot.orden') as $tramo)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary fs-6">{{ $tramo->pivot->orden }}</span>
                            </td>
                            <td>
                                <strong>{{ $tramo->aeropuertoOrigen->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $tramo->aeropuertoOrigen->ciudad }}</small>
                            </td>
                            <td class="text-center"><i class="bi bi-arrow-right text-primary"></i></td>
                            <td>
                                <strong>{{ $tramo->aeropuertoDestino->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $tramo->aeropuertoDestino->ciudad }}</small>
                            </td>
                            <td>{{ $tramo->duracion_estimada }}</td>
                            <td>
                                @if($tramo->subTramos->count() > 0)
                                    <span class="badge bg-warning text-dark">{{ $tramo->subTramos->count() }} escala(s)</span>
                                    @foreach($tramo->subTramos as $sub)
                                    <div><small class="text-muted">
                                        <i class="bi bi-arrow-return-right"></i>
                                        {{ $sub->aeropuertoOrigen->codigo_IATA }} → {{ $sub->aeropuertoDestino->codigo_IATA }}
                                        ({{ $sub->duracion_estimada }})
                                    </small></div>
                                    @endforeach
                                @else
                                    <span class="badge bg-success">Directo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('operador.rutas.tramos.detach', [$ruta, $tramo]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Quitar este tramo de la ruta?')" title="Quitar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Esta ruta aún no tiene tramos asignados.
                </div>
                @endif

                {{-- Formulario para agregar tramo --}}
                <hr>
                <h6 class="mb-3"><i class="bi bi-plus-lg"></i> Agregar Tramo a esta Ruta</h6>
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <ul class="nav nav-tabs mb-3" id="tabTramo" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-nuevo">
                            <i class="bi bi-plus-circle"></i> Nuevo tramo
                        </button>
                    </li>
                    @if($tramosDisponibles->count() > 0)
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existente">
                            <i class="bi bi-link-45deg"></i> Adjuntar tramo existente
                            <span class="badge bg-secondary ms-1">{{ $tramosDisponibles->count() }}</span>
                        </button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-nuevo">
                        <form method="POST" action="{{ route('operador.rutas.tramos.attach', $ruta) }}" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Origen</label>
                                <select class="form-select" name="aeropuerto_origen_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($aeropuertos as $ap)
                                    <option value="{{ $ap->id }}" {{ old('aeropuerto_origen_id') == $ap->id ? 'selected' : '' }}>
                                        {{ $ap->codigo_IATA }} — {{ $ap->ciudad }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Destino</label>
                                <select class="form-select" name="aeropuerto_destino_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($aeropuertos as $ap)
                                    <option value="{{ $ap->id }}" {{ old('aeropuerto_destino_id') == $ap->id ? 'selected' : '' }}>
                                        {{ $ap->codigo_IATA }} — {{ $ap->ciudad }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Duración</label>
                                <input type="time" class="form-control" name="duracion_estimada"
                                       value="{{ old('duracion_estimada') }}" required>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control" name="orden"
                                       min="1" value="{{ old('orden', $ruta->tramos->count() + 1) }}" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($tramosDisponibles->count() > 0)
                    <div class="tab-pane fade" id="tab-existente">
                        <form method="POST" action="{{ route('operador.rutas.tramos.attach', $ruta) }}" class="row g-3">
                            @csrf
                            <div class="col-md-7">
                                <label class="form-label">Tramo existente</label>
                                <select class="form-select" name="tramo_id" required>
                                    <option value="">Seleccionar tramo...</option>
                                    @foreach($tramosDisponibles as $td)
                                    <option value="{{ $td->id }}">
                                        {{ $td->aeropuertoOrigen->codigo_IATA }} → {{ $td->aeropuertoDestino->codigo_IATA }}
                                        — {{ $td->aeropuertoOrigen->ciudad }} / {{ $td->aeropuertoDestino->ciudad }}
                                        @if($td->subTramos->count() > 0)
                                            ({{ $td->subTramos->count() }} escala(s))
                                        @else
                                            (Directo)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control" name="orden"
                                       min="1" value="{{ $ruta->tramos->count() + 1 }}" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-link-45deg"></i> Adjuntar
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('operador.rutas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
            <a href="{{ route('operador.rutas.edit', $ruta) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Ruta
            </a>
        </div>

    </div>
</div>
@endsection
