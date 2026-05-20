@extends('layouts.app')

@section('titulo', 'Editar Programación')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Programación: {{ $programacion->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.programaciones.update', $programacion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="codigo_vuelo" class="form-label">Código de Vuelo</label>
                            <input type="text" class="form-control @error('codigo_vuelo') is-invalid @enderror"
                                   id="codigo_vuelo" name="codigo_vuelo"
                                   value="{{ old('codigo_vuelo', $programacion->codigo_vuelo) }}" required>
                            @error('codigo_vuelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="ruta_tramo_id" class="form-label">Ruta / Tramo</label>
                            <select class="form-select @error('ruta_tramo_id') is-invalid @enderror"
                                    id="ruta_tramo_id" name="ruta_tramo_id" required>
                                @foreach($rutaTramos as $rt)
                                    <option value="{{ $rt->id }}"
                                            data-duracion="{{ $rt->tramo->duracion_estimada }}"
                                            {{ old('ruta_tramo_id', $programacion->ruta_tramo_id) == $rt->id ? 'selected' : '' }}>
                                        {{ $rt->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $rt->ruta->aeropuertoDestino->codigo_IATA }}
                                        | Tramo {{ $rt->orden }}: {{ $rt->tramo->aeropuertoOrigen->codigo_IATA }} → {{ $rt->tramo->aeropuertoDestino->codigo_IATA }}
                                        @if($rt->tramo->subTramos->count() > 0)
                                            ({{ $rt->tramo->subTramos->count() }} escala(s))
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('ruta_tramo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="aeronave_id" class="form-label">Aeronave</label>
                            <select class="form-select @error('aeronave_id') is-invalid @enderror"
                                    id="aeronave_id" name="aeronave_id" required>
                                @foreach($aeronaves as $aeronave)
                                    <option value="{{ $aeronave->id }}" {{ old('aeronave_id', $programacion->aeronave_id) == $aeronave->id ? 'selected' : '' }}>
                                        {{ $aeronave->matricula }} - {{ $aeronave->modelo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aeronave_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="fecha_salida" class="form-label">Fecha Salida</label>
                            <input type="date" class="form-control @error('fecha_salida') is-invalid @enderror"
                                   id="fecha_salida" name="fecha_salida"
                                   value="{{ old('fecha_salida', $programacion->fecha_salida) }}" required>
                            @error('fecha_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="hora_salida" class="form-label">Hora Salida</label>
                            <input type="time" class="form-control @error('hora_salida') is-invalid @enderror"
                                   id="hora_salida" name="hora_salida"
                                   value="{{ old('hora_salida', $programacion->hora_salida) }}" required>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                            <input type="date" class="form-control @error('fecha_llegada') is-invalid @enderror"
                                   id="fecha_llegada" name="fecha_llegada"
                                   value="{{ old('fecha_llegada', $programacion->fecha_llegada) }}" required>
                            @error('fecha_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="hora_llegada" class="form-label">Hora Llegada</label>
                            <input type="time" class="form-control @error('hora_llegada') is-invalid @enderror"
                                   id="hora_llegada" name="hora_llegada"
                                   value="{{ old('hora_llegada', $programacion->hora_llegada) }}" required>
                            @error('hora_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Asientos Vendidos</label>
                            <p class="form-control-plaintext"><span class="badge bg-secondary fs-6">{{ $programacion->asientos_vendidos }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <p><span class="badge bg-primary fs-6">{{ $programacion->estado }}</span></p>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-tag"></i> Precios por Clase</h6>
                    @error('precios')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="row g-3 mb-3">
                        @foreach($tipoClases as $i => $tc)
                        @php
                            $precioActual = $programacion->precios->firstWhere('tipo_clase_id', $tc->id);
                        @endphp
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ $tc->nombre }}</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="1"
                                       class="form-control @error("precios.$i.precio") is-invalid @enderror"
                                       name="precios[{{ $i }}][precio]"
                                       value="{{ old("precios.$i.precio", $precioActual?->precio) }}" required>
                                <input type="hidden" name="precios[{{ $i }}][tipo_clase_id]" value="{{ $tc->id }}">
                                @error("precios.$i.precio")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.programaciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Programación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
