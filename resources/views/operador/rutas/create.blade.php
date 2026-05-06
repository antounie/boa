@extends('layouts.app')

@section('titulo', 'Crear Ruta')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Registrar Nueva Ruta</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.rutas.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="aeropuerto_origen_id" class="form-label">Aeropuerto de Origen</label>
                            <select class="form-select @error('aeropuerto_origen_id') is-invalid @enderror"
                                    id="aeropuerto_origen_id" name="aeropuerto_origen_id" required>
                                <option value="">Seleccionar origen...</option>
                                @foreach($aeropuertos as $aeropuerto)
                                    <option value="{{ $aeropuerto->id }}" {{ old('aeropuerto_origen_id') == $aeropuerto->id ? 'selected' : '' }}>
                                        {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }} ({{ $aeropuerto->pais }})
                                    </option>
                                @endforeach
                            </select>
                            @error('aeropuerto_origen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="aeropuerto_destino_id" class="form-label">Aeropuerto de Destino</label>
                            <select class="form-select @error('aeropuerto_destino_id') is-invalid @enderror"
                                    id="aeropuerto_destino_id" name="aeropuerto_destino_id" required>
                                <option value="">Seleccionar destino...</option>
                                @foreach($aeropuertos as $aeropuerto)
                                    <option value="{{ $aeropuerto->id }}" {{ old('aeropuerto_destino_id') == $aeropuerto->id ? 'selected' : '' }}>
                                        {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }} ({{ $aeropuerto->pais }})
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
                            <label for="distancia" class="form-label">Distancia (km)</label>
                            <input type="number" step="0.01" class="form-control @error('distancia') is-invalid @enderror"
                                   id="distancia" name="distancia" value="{{ old('distancia') }}" min="1" required>
                            @error('distancia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="duracion_estimada" class="form-label">Duración Estimada</label>
                            <input type="time" class="form-control @error('duracion_estimada') is-invalid @enderror"
                                   id="duracion_estimada" name="duracion_estimada" value="{{ old('duracion_estimada') }}" required>
                            @error('duracion_estimada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo de Ruta</label>
                            <select class="form-select @error('tipo') is-invalid @enderror"
                                    id="tipo" name="tipo" required>
                                <option value="">Seleccionar...</option>
                                <option value="Nacional" {{ old('tipo') == 'Nacional' ? 'selected' : '' }}>Nacional</option>
                                <option value="Internacional" {{ old('tipo') == 'Internacional' ? 'selected' : '' }}>Internacional</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.rutas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Ruta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection