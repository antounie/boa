@extends('layouts.app')

@section('titulo', 'Asignar Tripulante')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-plus"></i> Asignar {{ $cargo }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <strong>Vuelo:</strong> {{ $programacion->codigo_vuelo }} |
                    {{ $programacion->aeropuertoOrigen->codigo_IATA }} → {{ $programacion->aeropuertoDestino->codigo_IATA }} |
                    {{ $programacion->fecha_salida }} {{ $programacion->hora_salida }}
                </div>

                <form method="POST" action="{{ route('operador.tripulaciones.store') }}">
                    @csrf
                    <input type="hidden" name="programacion_vuelo_id" value="{{ $programacion->id }}">
                    <input type="hidden" name="cargo" value="{{ $cargo }}">

                    <div class="mb-3">
                        <label for="empleado_id" class="form-label">Seleccionar {{ $cargo }}</label>
                        @if($empleados->count() > 0)
                        <select class="form-select @error('empleado_id') is-invalid @enderror"
                                id="empleado_id" name="empleado_id" required>
                            <option value="">Seleccionar empleado...</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}" {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                                    {{ $empleado->nombre }} {{ $empleado->apellido }}
                                    @if($empleado->licencia)
                                        ({{ $empleado->licencia }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('empleado_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No hay {{ strtolower($cargo) }}s disponibles para este vuelo.
                        </div>
                        @endif
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.tripulaciones.index', ['programacion_id' => $programacion->id]) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        @if($empleados->count() > 0)
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Asignar {{ $cargo }}
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection