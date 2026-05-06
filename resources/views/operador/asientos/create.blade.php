@extends('layouts.app')

@section('titulo', 'Agregar Asiento')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Agregar Asiento - {{ $aeronave->matricula }}</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted">Asientos configurados: {{ $asientosActuales }}/{{ $aeronave->capacidad_total }}</p>

                <form method="POST" action="{{ route('operador.asientos.store') }}">
                    @csrf
                    <input type="hidden" name="aeronave_id" value="{{ $aeronave->id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Número de Asiento</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                   id="numero" name="numero" value="{{ old('numero') }}"
                                   style="text-transform: uppercase;" required>
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ej: 1A, 12B, 30F</small>
                        </div>
                        <div class="col-md-6">
                            <label for="fila" class="form-label">Fila</label>
                            <input type="number" class="form-control @error('fila') is-invalid @enderror"
                                   id="fila" name="fila" value="{{ old('fila') }}" min="1" required>
                            @error('fila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_clase_id" class="form-label">Tipo de Clase</label>
                        <select class="form-select @error('tipo_clase_id') is-invalid @enderror"
                                id="tipo_clase_id" name="tipo_clase_id" required>
                            <option value="">Seleccionar clase...</option>
                            @foreach($tipoClases as $clase)
                                <option value="{{ $clase->id }}" {{ old('tipo_clase_id') == $clase->id ? 'selected' : '' }}>
                                    {{ $clase->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_clase_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.asientos.index', ['aeronave_id' => $aeronave->id]) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Asiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection