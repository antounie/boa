@extends('layouts.app')

@section('titulo', 'Datos de Pasajeros')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Datos de Pasajeros</h5>
            </div>
            <div class="card-body p-4">

                {{-- Resumen del vuelo --}}
                <div class="alert alert-light border mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-airplane-fill text-primary fs-4"></i>
                        <div>
                            <strong>{{ $programacion->codigo_vuelo }}</strong>
                            @if($subTramo)
                                &mdash; {{ $subTramo->aeropuertoOrigen->codigo_IATA }} → {{ $subTramo->aeropuertoDestino->codigo_IATA }}
                            @else
                                &mdash; {{ $programacion->aeropuertoOrigen->codigo_IATA }} → {{ $programacion->aeropuertoDestino->codigo_IATA }}
                            @endif
                            <small class="d-block text-muted">{{ $programacion->fecha_salida }} · {{ $programacion->hora_salida }}</small>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('cliente.procesar.pago') }}">
                    @csrf
                    <input type="hidden" name="programacion_vuelo_id" value="{{ $programacion->id }}">
                    @if($subTramo)
                        <input type="hidden" name="sub_tramo_id" value="{{ $subTramo->id }}">
                    @endif
                    @foreach($asientos as $asiento)
                        <input type="hidden" name="asiento_ids[]" value="{{ $asiento->id }}">
                    @endforeach

                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Ingrese el nombre y apellido del pasajero para cada asiento seleccionado.
                    </p>

                    @foreach($asientos as $i => $asiento)
                        @php
                            $precioProg = $programacion->precios->firstWhere('tipo_clase_id', $asiento->tipo_clase_id);
                            $precio = $precioProg ? $precioProg->precio : ($programacion->precio_base * $asiento->tipoClase->multiplicador_precio);
                        @endphp
                        <div class="card mb-3 border-primary border-opacity-25">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <span class="fw-semibold">
                                    <i class="bi bi-seat me-1 text-primary"></i>
                                    Asiento {{ $asiento->numero }}
                                    <span class="badge bg-primary ms-1">{{ $asiento->tipoClase->nombre }}</span>
                                </span>
                                <span class="text-primary fw-bold">Bs. {{ number_format($precio, 2) }}</span>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('pasajeros.'.$asiento->id.'.nombre') is-invalid @enderror"
                                               name="pasajeros[{{ $asiento->id }}][nombre]"
                                               value="{{ old('pasajeros.'.$asiento->id.'.nombre', $i === 0 ? $cliente->nombre : '') }}"
                                               placeholder="Nombre" required>
                                        @error('pasajeros.'.$asiento->id.'.nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('pasajeros.'.$asiento->id.'.apellido') is-invalid @enderror"
                                               name="pasajeros[{{ $asiento->id }}][apellido]"
                                               value="{{ old('pasajeros.'.$asiento->id.'.apellido', $i === 0 ? $cliente->apellido : '') }}"
                                               placeholder="Apellido" required>
                                        @error('pasajeros.'.$asiento->id.'.apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('cliente.seleccionar.asiento', $programacion) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Volver a selección
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-credit-card me-2"></i>Continuar al pago
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
