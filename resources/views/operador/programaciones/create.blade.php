@extends('layouts.app')

@section('titulo', 'Crear Programación')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Nueva Programación de Vuelo</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.programaciones.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="vuelo_id" class="form-label">Vuelo</label>
                            <select class="form-select @error('vuelo_id') is-invalid @enderror"
                                    id="vuelo_id" name="vuelo_id" required>
                                <option value="">Seleccionar vuelo...</option>
                                @foreach($vuelos as $vuelo)
                                    <option value="{{ $vuelo->id }}"
                                            data-tipo="{{ $vuelo->tipo }}"
                                            data-es-hijo="{{ $vuelo->vuelo_padre_id ? 'true' : 'false' }}"
                                            {{ old('vuelo_id') == $vuelo->id ? 'selected' : '' }}>
                                        {{ $vuelo->codigo_vuelo }}
                                        @if($vuelo->vuelo_padre_id)
                                            (Tramo de {{ $vuelo->vueloPadre->codigo_vuelo }})
                                        @elseif($vuelo->tipo === 'ConEscalas')
                                            (Con Escalas)
                                        @else
                                            (Directo)
                                        @endif
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
                                <option value="">Seleccionar ruta...</option>
                                @foreach($rutas as $ruta)
                                    <option value="{{ $ruta->id }}" {{ old('ruta_id') == $ruta->id ? 'selected' : '' }}>
                                        {{ $ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ruta->aeropuertoDestino->codigo_IATA }}
                                        ({{ $ruta->aeropuertoOrigen->ciudad }} - {{ $ruta->aeropuertoDestino->ciudad }})
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
                                <option value="">Seleccionar aeronave...</option>
                                @foreach($aeronaves as $aeronave)
                                    <option value="{{ $aeronave->id }}" {{ old('aeronave_id') == $aeronave->id ? 'selected' : '' }}>
                                        {{ $aeronave->matricula }} - {{ $aeronave->modelo }} ({{ $aeronave->capacidad_total }} asientos)
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
                                   id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida') }}" required>
                            @error('fecha_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="hora_salida" class="form-label">Hora Salida</label>
                            <input type="time" class="form-control @error('hora_salida') is-invalid @enderror"
                                   id="hora_salida" name="hora_salida" value="{{ old('hora_salida') }}" required>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                            <input type="date" class="form-control @error('fecha_llegada') is-invalid @enderror"
                                   id="fecha_llegada" name="fecha_llegada" value="{{ old('fecha_llegada') }}" readonly required>
                            @error('fecha_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="msg_fecha_llegada">Se calcula automáticamente</small>
                        </div>
                        <div class="col-md-3">
                            <label for="hora_llegada" class="form-label">Hora Llegada</label>
                            <input type="time" class="form-control @error('hora_llegada') is-invalid @enderror"
                                   id="hora_llegada" name="hora_llegada" value="{{ old('hora_llegada') }}" readonly required>
                            @error('hora_llegada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="msg_hora_llegada">Se calcula automáticamente</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="precio_base" class="form-label">Precio Base (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('precio_base') is-invalid @enderror"
                                       id="precio_base" name="precio_base" value="{{ old('precio_base') }}" min="1" required>
                                @error('precio_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.programaciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Programación
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

    const horariosPadre = {
        @foreach($horariosPadre as $id => $horario)
            '{{ $id }}': {
                fecha_salida: '{{ $horario['fecha_salida'] }}',
                hora_salida: '{{ $horario['hora_salida'] }}',
                fecha_llegada: '{{ $horario['fecha_llegada'] }}',
                hora_llegada: '{{ $horario['hora_llegada'] }}',
                aeronave_id: '{{ $horario['aeronave_id'] }}'
            },
        @endforeach
    };

    function calcularLlegada() {
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

    function verificarTipoVuelo() {
        const vueloSelect = document.getElementById('vuelo_id');
        const selectedOption = vueloSelect.options[vueloSelect.selectedIndex];
        const tipoVuelo = selectedOption ? selectedOption.getAttribute('data-tipo') : '';
        const esHijo = selectedOption ? selectedOption.getAttribute('data-es-hijo') === 'true' : false;
        const vueloId = vueloSelect.value;

        const fechaSalida = document.getElementById('fecha_salida');
        const horaSalida = document.getElementById('hora_salida');
        const fechaLlegada = document.getElementById('fecha_llegada');
        const horaLlegada = document.getElementById('hora_llegada');
        const aeronaveSelect = document.getElementById('aeronave_id');
        const msgFecha = document.getElementById('msg_fecha_llegada');
        const msgHora = document.getElementById('msg_hora_llegada');

        if (tipoVuelo === 'ConEscalas' && !esHijo) {
            // Vuelo padre con escalas
            if (horariosPadre[vueloId]) {
                // Tiene tramos programados: llenar automáticamente y hacer readonly
                const datos = horariosPadre[vueloId];
                fechaSalida.value = datos.fecha_salida;
                horaSalida.value = datos.hora_salida;
                fechaLlegada.value = datos.fecha_llegada;
                horaLlegada.value = datos.hora_llegada;
                aeronaveSelect.value = datos.aeronave_id;

                fechaSalida.setAttribute('readonly', true);
                horaSalida.setAttribute('readonly', true);
                fechaLlegada.setAttribute('readonly', true);
                horaLlegada.setAttribute('readonly', true);
                aeronaveSelect.setAttribute('disabled', true);

                // Campo oculto para enviar aeronave_id cuando está disabled
                let hiddenAeronave = document.getElementById('hidden_aeronave_id');
                if (!hiddenAeronave) {
                    hiddenAeronave = document.createElement('input');
                    hiddenAeronave.type = 'hidden';
                    hiddenAeronave.id = 'hidden_aeronave_id';
                    hiddenAeronave.name = 'aeronave_id';
                    aeronaveSelect.parentNode.appendChild(hiddenAeronave);
                }
                hiddenAeronave.value = datos.aeronave_id;

                msgFecha.textContent = 'Calculado desde los tramos de escala';
                msgHora.textContent = 'Calculado desde los tramos de escala';
            } else {
                // No tiene tramos programados: campos editables
                fechaSalida.removeAttribute('readonly');
                horaSalida.removeAttribute('readonly');
                fechaLlegada.removeAttribute('readonly');
                horaLlegada.removeAttribute('readonly');
                aeronaveSelect.removeAttribute('disabled');

                let hiddenAeronave = document.getElementById('hidden_aeronave_id');
                if (hiddenAeronave) hiddenAeronave.remove();

                msgFecha.textContent = 'Ingrese considerando el tiempo total con escalas';
                msgHora.textContent = 'Hora de llegada al destino final';
            }
        } else {
            // Vuelo directo o tramo hijo: calculo automático
            fechaSalida.removeAttribute('readonly');
            horaSalida.removeAttribute('readonly');
            fechaLlegada.setAttribute('readonly', true);
            horaLlegada.setAttribute('readonly', true);
            aeronaveSelect.removeAttribute('disabled');

            let hiddenAeronave = document.getElementById('hidden_aeronave_id');
            if (hiddenAeronave) hiddenAeronave.remove();

            msgFecha.textContent = 'Se calcula automáticamente';
            msgHora.textContent = 'Se calcula automáticamente';
            calcularLlegada();
        }
    }

    document.getElementById('vuelo_id').addEventListener('change', verificarTipoVuelo);
    document.getElementById('ruta_id').addEventListener('change', function() {
        const vueloSelect = document.getElementById('vuelo_id');
        const selectedOption = vueloSelect.options[vueloSelect.selectedIndex];
        const tipoVuelo = selectedOption ? selectedOption.getAttribute('data-tipo') : '';
        const esHijo = selectedOption ? selectedOption.getAttribute('data-es-hijo') === 'true' : false;
        if (tipoVuelo !== 'ConEscalas' || esHijo) {
            calcularLlegada();
        }
    });
    document.getElementById('fecha_salida').addEventListener('change', function() {
        const vueloSelect = document.getElementById('vuelo_id');
        const selectedOption = vueloSelect.options[vueloSelect.selectedIndex];
        const tipoVuelo = selectedOption ? selectedOption.getAttribute('data-tipo') : '';
        const esHijo = selectedOption ? selectedOption.getAttribute('data-es-hijo') === 'true' : false;
        if (tipoVuelo !== 'ConEscalas' || esHijo) {
            calcularLlegada();
        }
    });
    document.getElementById('hora_salida').addEventListener('change', function() {
        const vueloSelect = document.getElementById('vuelo_id');
        const selectedOption = vueloSelect.options[vueloSelect.selectedIndex];
        const tipoVuelo = selectedOption ? selectedOption.getAttribute('data-tipo') : '';
        const esHijo = selectedOption ? selectedOption.getAttribute('data-es-hijo') === 'true' : false;
        if (tipoVuelo !== 'ConEscalas' || esHijo) {
            calcularLlegada();
        }
    });

    verificarTipoVuelo();
</script>
@endpush

@endsection