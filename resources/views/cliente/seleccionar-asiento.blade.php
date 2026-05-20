@extends('layouts.app')

@section('titulo', 'Seleccionar Asiento')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<h2 class="mb-3"><i class="bi bi-grid-3x3"></i> Seleccionar Asientos</h2>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            {{ $programacion->codigo_vuelo }} |
            @if($subTramo ?? null)
                <span class="badge bg-warning text-dark me-1">Tramo parcial</span>
                {{ $subTramo->aeropuertoOrigen->codigo_IATA }} → {{ $subTramo->aeropuertoDestino->codigo_IATA }}
            @else
                {{ $programacion->aeropuertoOrigen->codigo_IATA }} → {{ $programacion->aeropuertoDestino->codigo_IATA }}
            @endif
            | {{ $programacion->fecha_salida }} {{ $programacion->hora_salida }}
        </h5>
    </div>
</div>

<div class="row g-3">
    {{-- Columna del mapa --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Precios por clase --}}
                <div class="row mb-3 g-2">
                    @foreach($clases as $clase)
                    @php
                        $colorClaseH = $clase->nombre === 'Económica' ? 'success' : ($clase->nombre === 'Ejecutiva' ? 'primary' : 'warning');
                        $precioClase = $programacion->precios->firstWhere('tipo_clase_id', $clase->id);
                        $precioMostrar = $precioClase ? $precioClase->precio : ($programacion->precio_base * $clase->multiplicador_precio);
                    @endphp
                    <div class="col">
                        <div class="card border-{{ $colorClaseH }} text-center">
                            <div class="card-body py-2 px-2">
                                <h6 class="mb-1 small">{{ $clase->nombre }}</h6>
                                <h5 class="mb-0 text-{{ $colorClaseH }}">
                                    Bs. {{ number_format($precioMostrar, 2) }}
                                </h5>
                                <small class="text-muted">{{ $asientos->filter(fn($ap) => $ap->asiento->tipo_clase_id == $clase->id && $ap->estado == 'Disponible')->count() }} disponibles</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Filtro por clase --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small">Filtrar por clase:</label>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-dark active filtro-clase" data-clase="todas">Todas</button>
                        @foreach($clases as $clase)
                        @php $colorF = $clase->nombre === 'Económica' ? 'success' : ($clase->nombre === 'Ejecutiva' ? 'primary' : 'warning'); @endphp
                        <button type="button" class="btn btn-outline-{{ $colorF }} filtro-clase" data-clase="{{ $clase->id }}">
                            {{ $clase->nombre }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Leyenda --}}
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <span class="badge bg-success"><i class="bi bi-square-fill"></i> Disponible</span>
                    <span class="badge bg-danger"><i class="bi bi-square-fill"></i> Ocupado</span>
                    <span class="badge bg-warning text-dark"><i class="bi bi-square-fill"></i> Bloqueado</span>
                    <span class="badge" style="background:#1a237e;"><i class="bi bi-square-fill"></i> Seleccionado</span>
                </div>

                {{-- Mapa de asientos --}}
                <div id="mapaAsientos">
                    @php $filaActual = 0; @endphp
                    @foreach($asientos as $ap)
                        @if($ap->asiento->fila != $filaActual)
                            @if($filaActual != 0)
                                </div>
                            @endif
                            @php $filaActual = $ap->asiento->fila; @endphp
                            <div class="d-flex align-items-center mb-2 fila-asientos" data-fila="{{ $filaActual }}">
                                <span class="badge bg-dark me-3 flex-shrink-0" style="width: 40px;">F{{ $filaActual }}</span>
                        @endif

                        @php
                            $precioProg = $programacion->precios->firstWhere('tipo_clase_id', $ap->asiento->tipo_clase_id);
                            $precioAsiento = $precioProg ? $precioProg->precio : ($programacion->precio_base * $ap->asiento->tipoClase->multiplicador_precio);
                            $colorClase = $ap->asiento->tipoClase->nombre === 'Económica' ? 'success' : ($ap->asiento->tipoClase->nombre === 'Ejecutiva' ? 'primary' : 'warning');
                        @endphp

                        <div class="me-1 asiento-item" data-clase-id="{{ $ap->asiento->tipo_clase_id }}">
                            @if($ap->estado === 'Disponible')
                                <button type="button"
                                    class="btn btn-{{ $colorClase }} btn-sm asiento-toggle position-relative"
                                    style="width: 60px; height: 45px;"
                                    data-asiento-id="{{ $ap->asiento->id }}"
                                    data-numero="{{ $ap->asiento->numero }}"
                                    data-clase="{{ $ap->asiento->tipoClase->nombre }}"
                                    data-precio="{{ $precioAsiento }}"
                                    title="{{ $ap->asiento->numero }} | {{ $ap->asiento->tipoClase->nombre }} | Bs. {{ number_format($precioAsiento, 2) }}">
                                    {{ $ap->asiento->numero }}
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark" style="font-size: 0.5rem;">
                                        {{ substr($ap->asiento->tipoClase->nombre, 0, 3) }}
                                    </span>
                                </button>
                            @elseif($ap->estado === 'Ocupado')
                                <button class="btn btn-danger btn-sm" style="width: 60px; height: 45px;" disabled
                                        title="{{ $ap->asiento->numero }} - Ocupado">
                                    {{ $ap->asiento->numero }}
                                </button>
                            @else
                                <button class="btn btn-warning btn-sm" style="width: 60px; height: 45px;" disabled
                                        title="{{ $ap->asiento->numero }} - Bloqueado">
                                    {{ $ap->asiento->numero }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                    @if($filaActual != 0)
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <a href="{{ route('cliente.buscar') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver a búsqueda
        </a>
    </div>

    {{-- Panel carrito --}}
    <div class="col-lg-4">
        <div class="sticky-top" style="top: 72px;">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-cart3 me-2"></i>Mi selección</h6>
                    <span class="badge bg-light text-primary fs-6" id="contadorBadge">0</span>
                </div>
                <div class="card-body p-2" id="resumenAsientos" style="min-height: 80px; max-height: 340px; overflow-y: auto;">
                    <p class="text-muted text-center small py-3 mb-0" id="mensajeVacio">
                        <i class="bi bi-hand-index-thumb d-block fs-3 mb-1"></i>
                        Haga clic en un asiento disponible para seleccionarlo
                    </p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between fw-bold mb-3 fs-5 border-top pt-2">
                        <span>Total:</span>
                        <span id="totalMonto" class="text-primary">Bs. 0.00</span>
                    </div>
                    <form id="formCompra" action="{{ route('cliente.datos.pasajeros') }}" method="POST">
                        @csrf
                        <input type="hidden" name="programacion_vuelo_id" value="{{ $programacion->id }}">
                        @if($subTramo ?? null)
                            <input type="hidden" name="sub_tramo_id" value="{{ $subTramo->id }}">
                        @endif
                        <div id="asientoInputs"></div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="btnProceder" disabled>
                            <i class="bi bi-credit-card me-2"></i>Proceder al pago
                        </button>
                    </form>
                    <p class="text-muted small text-center mt-2 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Máximo 10 asientos por compra
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .asiento-seleccionado {
        background-color: #1a237e !important;
        border-color: #1a237e !important;
        color: #fff !important;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.35) !important;
        transform: scale(1.08);
    }
    .asiento-toggle { transition: transform 0.12s ease, box-shadow 0.12s ease; }
    .asiento-toggle:hover:not(.asiento-seleccionado) { transform: scale(1.05); }
</style>
@endpush

@push('scripts')
<script>
const MAX_ASIENTOS = 10;
let selectedSeats = {};

function updatePanel() {
    const ids = Object.keys(selectedSeats);
    document.getElementById('contadorBadge').textContent = ids.length;

    const asientoInputs = document.getElementById('asientoInputs');
    asientoInputs.innerHTML = '';

    const mensajeVacio = document.getElementById('mensajeVacio');
    const resumen = document.getElementById('resumenAsientos');
    const totalEl = document.getElementById('totalMonto');
    const btnProceder = document.getElementById('btnProceder');

    if (ids.length === 0) {
        mensajeVacio.style.display = '';
        totalEl.textContent = 'Bs. 0.00';
        btnProceder.disabled = true;
        return;
    }

    mensajeVacio.style.display = 'none';

    let total = 0;
    let html = '<ul class="list-unstyled mb-0">';
    ids.forEach(id => {
        const s = selectedSeats[id];
        total += s.precio;
        html += `<li class="d-flex justify-content-between align-items-center border-bottom py-1 px-1">
            <div>
                <span class="fw-semibold">${s.numero}</span>
                <small class="text-muted ms-1">${s.clase}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <small class="text-primary fw-semibold">Bs. ${s.precio.toFixed(2)}</small>
                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-1" style="font-size:.65rem;"
                    onclick="deseleccionar('${id}')"><i class="bi bi-x"></i></button>
            </div>
        </li>`;

        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'asiento_ids[]';
        inp.value = id;
        asientoInputs.appendChild(inp);
    });
    html += '</ul>';

    resumen.innerHTML = html;
    resumen.appendChild(mensajeVacio);

    totalEl.textContent = 'Bs. ' + total.toFixed(2);
    btnProceder.disabled = false;
}

function deseleccionar(id) {
    delete selectedSeats[id];
    const btn = document.querySelector(`.asiento-toggle[data-asiento-id="${id}"]`);
    if (btn) btn.classList.remove('asiento-seleccionado');
    updatePanel();
}

document.querySelectorAll('.asiento-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-asiento-id');

        if (selectedSeats[id]) {
            deseleccionar(id);
            return;
        }

        if (Object.keys(selectedSeats).length >= MAX_ASIENTOS) {
            alert('Solo puede seleccionar un máximo de ' + MAX_ASIENTOS + ' asientos por compra.');
            return;
        }

        selectedSeats[id] = {
            numero: this.getAttribute('data-numero'),
            clase: this.getAttribute('data-clase'),
            precio: parseFloat(this.getAttribute('data-precio')),
        };
        this.classList.add('asiento-seleccionado');
        updatePanel();
    });
});

document.querySelectorAll('.filtro-clase').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtro-clase').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const claseId = this.getAttribute('data-clase');
        document.querySelectorAll('.asiento-item').forEach(function(item) {
            item.style.display = (claseId === 'todas' || item.getAttribute('data-clase-id') === claseId) ? '' : 'none';
        });
    });
});
</script>
@endpush