@extends('layouts.app')

@section('titulo', 'Buscar Vuelos')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-search"></i> Buscar Vuelos Disponibles</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('cliente.buscar.resultados') }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Origen</label>
                        <select class="form-select @error('origen') is-invalid @enderror" name="origen" required>
                            <option value="">Seleccionar origen...</option>
                            @foreach($aeropuertos as $aeropuerto)
                                <option value="{{ $aeropuerto->id }}" {{ old('origen') == $aeropuerto->id ? 'selected' : '' }}>
                                    {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }}
                                </option>
                            @endforeach
                        </select>
                        @error('origen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Destino</label>
                        <select class="form-select @error('destino') is-invalid @enderror" name="destino" required>
                            <option value="">Seleccionar destino...</option>
                            @foreach($aeropuertos as $aeropuerto)
                                <option value="{{ $aeropuerto->id }}" {{ old('destino') == $aeropuerto->id ? 'selected' : '' }}>
                                    {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }}
                                </option>
                            @endforeach
                        </select>
                        @error('destino')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               name="fecha" value="{{ old('fecha') }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @isset($vuelos)
            @if($vuelos->count() > 0)
                <h5 class="mb-3">Se encontraron {{ $vuelos->count() }} vuelo(s) disponible(s)</h5>
                @foreach($vuelos as $vuelo)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <h4 class="text-primary mb-0">{{ $vuelo->vuelo->codigo_vuelo }}</h4>
                                @if($vuelo->vuelo->tipo === 'ConEscalas')
                                    <small class="badge bg-warning text-dark">Con Escalas</small>
                                @else
                                    <small class="badge bg-primary">Directo</small>
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>{{ $vuelo->ruta->aeropuertoOrigen->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $vuelo->ruta->aeropuertoOrigen->ciudad }}</small>
                                <br>{{ $vuelo->hora_salida }}
                            </div>
                            <div class="col-md-1 text-center">
                                <i class="bi bi-arrow-right text-primary fs-4"></i>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>{{ $vuelo->ruta->aeropuertoDestino->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $vuelo->ruta->aeropuertoDestino->ciudad }}</small>
                                <br>{{ $vuelo->hora_llegada }}
                            </div>
                            <div class="col-md-1 text-center">
                                <h4 class="text-success mb-0">${{ number_format($vuelo->precio_base, 2) }}</h4>
                                <small class="text-muted">{{ $vuelo->asientos_disponibles }} asientos</small>
                            </div>
                            <div class="col-md-2 text-center">
                                @if($vuelo->asientos_disponibles > 0)
                                    <a href="{{ route('cliente.seleccionar.asiento', $vuelo) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Seleccionar
                                    </a>
                                @else
                                    <span class="badge bg-danger">Agotado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> No se encontraron vuelos disponibles para la fecha y ruta seleccionada.
                </div>
            @endif
        @endisset
    </div>
</div>
@endsection