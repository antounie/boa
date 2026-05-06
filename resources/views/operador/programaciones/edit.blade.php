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
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Programación: {{ $programacion->vuelo->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.programaciones.update', $programacion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="vuelo_id" class="form-label">Vuelo</label>
                            <select class="form-select @error('vuelo_id') is-invalid @enderror"
                                    id="vuelo_id" name="vuelo_id" required>
                                @foreach($vuelos as $vuelo)
                                    <option value="{{ $vuelo->id }}"
                                            data-es-padre-escalas="{{ $vuelo->tipo === 'ConEscalas' && !$vuelo->vuelo_padre_id ? 'true' : 'false' }}"
                                            {{ old('vuelo_id', $programacion->vuelo_id) == $vuelo->id ? 'selected' : '' }}>
                                        {{ $vuelo->codigo_vuelo }} ({{ $vuelo->tipo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vuelo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="ruta_id" class="form-label">Ruta</label>
                            <select class="form-select @error('ruta_id') is-invalid @enderror"
                                    id="ruta_id" name="ruta_id" required>
                                @foreach($rutas as $ruta)
                                    <option value="{{ $ruta->id }}" {{ old('ruta_id', $programacion->ruta_id) == $ruta->id ? 'selected' : '' }}>
                                        {{ $ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ruta->aeropuertoDestino->codigo_IATA }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruta_id')
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
                                   id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida', $programacion->fecha_salida) }}" required>
                            @error('fecha_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="hora_salida" class="form-label">Hora Salida</label>
                            <input type="time" class="form-control @error('hora_salida') is-invalid @enderror"
                                   id="hora_salida" name="hora_salida" value="{{ old('hora_salida', $programacion->hora_salida) }}" required>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                            <input type="date" class="form-control @error('fecha_llegada') is-invalid @enderror"
                                   id="fecha_llegada" name="fecha_llegada" value="{{ old('fecha_llegada', $programacion->fecha_llegada) }}" required>
                            @error('fecha_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="hora_llegada" class="form-label">Hora Llegada</label>
                            <input type="time" class="form-control @error('hora_llegada') is-invalid @enderror"
                                   id="hora_llegada" name="hora_llegada" value="{{ old('hora_llegada', $programacion->hora_llegada) }}" required>
                            @error('hora_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="precio_base" class="form-label">Precio Base (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('precio_base') is-invalid @enderror"
                                       id="precio_base" name="precio_base" value="{{ old('precio_base', $programacion->precio_base) }}" min="1" required>
                                @error('precio_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asientos Vendidos</label>
                            <p class="form-control-plaintext"><span class="badge bg-secondary fs-6">{{ $programacion->asientos_vendidos }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <p><span class="badge bg-primary fs-6">{{ $programacion->estado }}</span></p>
                        </div>
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

@push('scripts')
<script>
    const rutasDuracion = {
        @foreach($rutas as $ruta)
            '{{ $ruta->id }}': '{{ $ruta->duracion_estimada }}',
        @endforeach
    };

    function esPadreConEscalas() {
        const vueloSelect = document.getElementById('vuelo_id');
        const selectedOption = vueloSelect.options[vueloSelect.selectedIndex];
        return selectedOption ? selectedOption.getAttribute('data-es-padre-escalas') === 'true' : false;
    }

    function calcularLlegada() {
        if (esPadreConEscalas()) return;

        const rutaId = document.getElementById('ruta_id').value;
        const fechaSalida = document.getElementById('fecha_salida').value;
        const horaSalida = document.getElementById('hora_salida').value;

        if (rutaId && fechaSalida && horaSalida && rutasDuracion[rutaId]) {
            const duracion = rutasDuracion[rutaId];
            const partesDuracion = duracion.split(':');
            const horasDuracion = parseInt(partesDuracion[0]);
            const minutosDuracion = parseInt(partesDuracion[1]);

            const salida = new Date(fechaSalida + 'T' + horaSalida);
            salida.setHours(salida.getHours() + horasDuracion);
            salida.setMinutes(salida.getMinutes() + minutosDuracion);

            const anio = salida.getFullYear();
            const mes = String(salida.getMonth() + 1).padStart(2, '0');
            const dia = String(salida.getDate()).padStart(2, '0');
            const hora = String(salida.getHours()).padStart(2, '0');
            const minuto = String(salida.getMinutes()).padStart(2, '0');

            document.getElementById('fecha_llegada').value = `${anio}-${mes}-${dia}`;
            document.getElementById('hora_llegada').value = `${hora}:${minuto}`;
        }
    }

    document.getElementById('vuelo_id').addEventListener('change', calcularLlegada);
    document.getElementById('ruta_id').addEventListener('change', calcularLlegada);
    document.getElementById('fecha_salida').addEventListener('change', calcularLlegada);
    document.getElementById('hora_salida').addEventListener('change', calcularLlegada);
</script>
@endpush

@endsection