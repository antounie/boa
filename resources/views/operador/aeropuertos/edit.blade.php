@extends('layouts.app')

@section('titulo', 'Editar Aeropuerto')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Aeropuerto: {{ $aeropuerto->codigo_IATA }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.aeropuertos.update', $aeropuerto) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="codigo_IATA" class="form-label">Código IATA</label>
                        <input type="text" class="form-control @error('codigo_IATA') is-invalid @enderror"
                               id="codigo_IATA" name="codigo_IATA" value="{{ old('codigo_IATA', $aeropuerto->codigo_IATA) }}"
                               maxlength="3" style="text-transform: uppercase;" required>
                        @error('codigo_IATA')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Aeropuerto</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                               id="nombre" name="nombre" value="{{ old('nombre', $aeropuerto->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control @error('ciudad') is-invalid @enderror"
                                   id="ciudad" name="ciudad" value="{{ old('ciudad', $aeropuerto->ciudad) }}" required>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control @error('pais') is-invalid @enderror"
                                   id="pais" name="pais" value="{{ old('pais', $aeropuerto->pais) }}" required>
                            @error('pais')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.aeropuertos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Aeropuerto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection