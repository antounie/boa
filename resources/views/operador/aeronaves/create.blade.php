@extends('layouts.app')

@section('titulo', 'Registrar Aeronave')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Registrar Nueva Aeronave</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.aeronaves.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control @error('matricula') is-invalid @enderror"
                                   id="matricula" name="matricula" value="{{ old('matricula') }}"
                                   style="text-transform: uppercase;" required>
                            @error('matricula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ej: CP-2901</small>
                        </div>
                        <div class="col-md-6">
                            <label for="capacidad_total" class="form-label">Capacidad Total</label>
                            <input type="number" class="form-control @error('capacidad_total') is-invalid @enderror"
                                   id="capacidad_total" name="capacidad_total" value="{{ old('capacidad_total') }}"
                                   min="1" required>
                            @error('capacidad_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Número total de asientos</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                               id="modelo" name="modelo" value="{{ old('modelo') }}" required>
                        @error('modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Ej: Boeing 737-300</small>
                    </div>

                    <div class="mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control @error('fabricante') is-invalid @enderror"
                               id="fabricante" name="fabricante" value="{{ old('fabricante') }}" required>
                        @error('fabricante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Ej: Boeing, Airbus</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.aeronaves.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Aeronave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection