@extends('layouts.app')

@section('titulo', 'Registrar Aeropuerto')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Registrar Nuevo Aeropuerto</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.aeropuertos.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo_IATA" class="form-label">Código IATA</label>
                        <input type="text" class="form-control @error('codigo_IATA') is-invalid @enderror"
                               id="codigo_IATA" name="codigo_IATA" value="{{ old('codigo_IATA') }}"
                               maxlength="3" style="text-transform: uppercase;" required>
                        @error('codigo_IATA')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">3 letras mayúsculas (ej: VVI, LPB, SRE)</small>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Aeropuerto</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                               id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control @error('ciudad') is-invalid @enderror"
                                   id="ciudad" name="ciudad" value="{{ old('ciudad') }}" required>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control @error('pais') is-invalid @enderror"
                                   id="pais" name="pais" value="{{ old('pais') }}" required>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Aeropuerto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection