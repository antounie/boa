@extends('layouts.app')

@section('titulo', 'Reprogramar Vuelo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('operador.programaciones.show', $programacion) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0 fw-bold" style="font-size:1.35rem;color:var(--text-primary)">
                <i class="bi bi-calendar-check me-2" style="color:var(--accent)"></i>
                Reprogramar Vuelo <span style="color:var(--accent)">{{ $programacion->codigo_vuelo }}</span>
            </h2>
        </div>

        {{-- Info del vuelo actual --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-2">
                <span class="fw-semibold small"><i class="bi bi-info-circle me-1"></i>Información Actual del Vuelo</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="text-muted small">Ruta</div>
                        <div class="fw-bold">
                            {{ $programacion->aeropuertoOrigen->codigo_IATA }}
                            <i class="bi bi-arrow-right"></i>
                            {{ $programacion->aeropuertoDestino->codigo_IATA }}
                        </div>
                        <div class="text-muted small">{{ $programacion->aeropuertoOrigen->ciudad }} → {{ $programacion->aeropuertoDestino->ciudad }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Fecha y Hora Salida</div>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($programacion->fecha_salida)->format('d/m/Y') }}</div>
                        <div class="text-muted small">{{ $programacion->hora_salida }} hrs</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Estado / Asientos</div>
                        <span class="badge bg-{{ $programacion->estado === 'Programado' ? 'primary' : 'warning text-dark' }}">
                            {{ $programacion->estado }}
                        </span>
                        <div class="text-muted small mt-1">{{ $programacion->asientos_vendidos }} vendidos</div>
                    </div>
                </div>

                @if($programacion->fecha_original)
                <div class="alert alert-warning mt-3 mb-0 py-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Este vuelo ya fue reprogramado. Fecha original:
                    <strong>{{ \Carbon\Carbon::parse($programacion->fecha_original)->format('d/m/Y') }} — {{ $programacion->hora_original }} hrs</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Formulario --}}
        <div class="card shadow-sm">
            <div class="card-header py-2">
                <span class="fw-semibold small"><i class="bi bi-pencil me-1"></i>Nueva Programación</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.programaciones.guardar-reprogramacion', $programacion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Nueva Fecha de Salida</label>
                            <input type="date" name="fecha_salida" class="form-control @error('fecha_salida') is-invalid @enderror"
                                   value="{{ old('fecha_salida', $programacion->fecha_salida) }}" required>
                            @error('fecha_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Nueva Hora de Salida</label>
                            <input type="time" name="hora_salida" class="form-control @error('hora_salida') is-invalid @enderror"
                                   value="{{ old('hora_salida', $programacion->hora_salida) }}" required>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Motivo de la Reprogramación</label>
                        <textarea name="motivo_reprogramacion" rows="3"
                                  class="form-control @error('motivo_reprogramacion') is-invalid @enderror"
                                  placeholder="Ej: Condiciones climáticas adversas, mantenimiento de aeronave..."
                                  required>{{ old('motivo_reprogramacion') }}</textarea>
                        @error('motivo_reprogramacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($programacion->asientos_vendidos > 0)
                    <div class="alert alert-info py-2 small mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        Este vuelo tiene <strong>{{ $programacion->asientos_vendidos }} pasajero(s)</strong> con tickets emitidos.
                        Sus tickets se actualizarán automáticamente con la nueva fecha y hora.
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.programaciones.show', $programacion) }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4">
                            <i class="bi bi-calendar-check me-1"></i>Confirmar Reprogramación
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
