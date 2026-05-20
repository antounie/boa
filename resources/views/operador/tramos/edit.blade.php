@extends('layouts.app')

@section('titulo', 'Editar Tramo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Tramo: {{ $tramo->aeropuertoOrigen->codigo_IATA }} → {{ $tramo->aeropuertoDestino->codigo_IATA }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.tramos.update', $tramo) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="tramo_padre_id" class="form-label">Tramo Padre <small class="text-muted">(opcional)</small></label>
                        <select class="form-select @error('tramo_padre_id') is-invalid @enderror"
                                id="tramo_padre_id" name="tramo_padre_id">
                            <option value="">— Tramo raíz (sin padre) —</option>
                            @foreach($tramosPadres as $padre)
                                <option value="{{ $padre->id }}" {{ old('tramo_padre_id', $tramo->tramo_padre_id) == $padre->id ? 'selected' : '' }}>
                                    {{ $padre->aeropuertoOrigen->codigo_IATA }} → {{ $padre->aeropuertoDestino->codigo_IATA }}
                                    ({{ $padre->aeropuertoOrigen->ciudad }} - {{ $padre->aeropuertoDestino->ciudad }})
                                </option>
                            @endforeach
                        </select>
                        @error('tramo_padre_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="aeropuerto_origen_id" class="form-label">Aeropuerto Origen</label>
                            <select class="form-select @error('aeropuerto_origen_id') is-invalid @enderror"
                                    id="aeropuerto_origen_id" name="aeropuerto_origen_id" required>
                                @foreach($aeropuertos as $aeropuerto)
                                    <option value="{{ $aeropuerto->id }}" {{ old('aeropuerto_origen_id', $tramo->aeropuerto_origen_id) == $aeropuerto->id ? 'selected' : '' }}>
                                        {{ $aeropuerto->codigo_IATA }} — {{ $aeropuerto->ciudad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aeropuerto_origen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="aeropuerto_destino_id" class="form-label">Aeropuerto Destino</label>
                            <select class="form-select @error('aeropuerto_destino_id') is-invalid @enderror"
                                    id="aeropuerto_destino_id" name="aeropuerto_destino_id" required>
                                @foreach($aeropuertos as $aeropuerto)
                                    <option value="{{ $aeropuerto->id }}" {{ old('aeropuerto_destino_id', $tramo->aeropuerto_destino_id) == $aeropuerto->id ? 'selected' : '' }}>
                                        {{ $aeropuerto->codigo_IATA }} — {{ $aeropuerto->ciudad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aeropuerto_destino_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="duracion_estimada" class="form-label">Duración de vuelo</label>
                            @if($tramo->subTramos->count() > 0)
                                <input type="time" class="form-control bg-light" id="duracion_estimada"
                                       value="{{ $tramo->duracion_estimada }}" readonly>
                                <input type="hidden" name="duracion_estimada" value="{{ $tramo->duracion_estimada }}">
                                <small class="text-muted">Calculada automáticamente desde los sub-tramos.</small>
                            @else
                                <input type="time" class="form-control @error('duracion_estimada') is-invalid @enderror"
                                       id="duracion_estimada" name="duracion_estimada"
                                       value="{{ old('duracion_estimada', $tramo->duracion_estimada) }}" required>
                                @error('duracion_estimada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label for="tiempo_escala" class="form-label">
                                Tiempo de escala <small class="text-muted">(opcional)</small>
                            </label>
                            <input type="time" class="form-control @error('tiempo_escala') is-invalid @enderror"
                                   id="tiempo_escala" name="tiempo_escala"
                                   value="{{ old('tiempo_escala', $tramo->tiempo_escala) }}">
                            <small class="text-muted">Espera en destino antes del siguiente tramo.</small>
                            @error('tiempo_escala')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="orden" class="form-label">Orden</label>
                            <input type="number" class="form-control @error('orden') is-invalid @enderror"
                                   id="orden" name="orden" min="1"
                                   value="{{ old('orden', $tramo->orden) }}" required>
                            @error('orden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.tramos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Tramo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
