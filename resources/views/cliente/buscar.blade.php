@extends('layouts.app')

@section('titulo', 'Buscar Vuelos')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-search"></i> Buscar Vuelos Disponibles</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('cliente.buscar.resultados') }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Origen</label>
                        <select class="form-select @error('origen') is-invalid @enderror" name="origen" required>
                            <option value="">Seleccionar origen...</option>
                            @foreach($aeropuertos as $aeropuerto)
                                <option value="{{ $aeropuerto->id }}" {{ old('origen') == $aeropuerto->id ? 'selected' : '' }}>
                                    {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }}
                                </option>
                            @endforeach
                        </select>
                        @error('origen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Destino</label>
                        <select class="form-select @error('destino') is-invalid @enderror" name="destino" required>
                            <option value="">Seleccionar destino...</option>
                            @foreach($aeropuertos as $aeropuerto)
                                <option value="{{ $aeropuerto->id }}" {{ old('destino') == $aeropuerto->id ? 'selected' : '' }}>
                                    {{ $aeropuerto->codigo_IATA }} - {{ $aeropuerto->ciudad }}
                                </option>
                            @endforeach
                        </select>
                        @error('destino')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               name="fecha" value="{{ old('fecha') }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @isset($vuelos)
        @php
            $totalResultados = $vuelos->count() + ($resultadosParciales ?? collect())->count();
        @endphp

        @if($totalResultados > 0)
            <h5 class="mb-3">Se encontraron {{ $totalResultados }} opción(es) disponible(s)</h5>

            {{-- Vuelos de ruta completa --}}
            @if($vuelos->count() > 0)
                <p class="text-muted small fw-semibold mb-2"><i class="bi bi-airplane-fill me-1"></i>Vuelo completo</p>
                @foreach($vuelos as $vuelo)
                @php
                    $subTramos = $vuelo->rutaTramo?->tramo?->subTramos->sortBy('orden') ?? collect();
                    $tieneEscalas = $subTramos->count() > 0;
                    $duracionTotal = $vuelo->rutaTramo?->tramo?->duracion_estimada ?? '—';
                    $precioMinimo = $vuelo->precios->min('precio') ?? $vuelo->precio_base;
                @endphp
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <h4 class="text-primary mb-0">{{ $vuelo->codigo_vuelo }}</h4>
                                @if($tieneEscalas)
                                    <small class="badge bg-warning text-dark">{{ $subTramos->count() }} escala(s)</small>
                                @else
                                    <small class="badge bg-success">Directo</small>
                                @endif
                            </div>

                            {{-- Origen --}}
                            <div class="col-md-2 text-center">
                                <div class="fw-bold fs-5">{{ $vuelo->aeropuertoOrigen->codigo_IATA }}</div>
                                <small class="text-muted">{{ $vuelo->aeropuertoOrigen->ciudad }}</small>
                                <div class="text-primary fw-semibold">{{ substr($vuelo->hora_salida, 0, 5) }}</div>
                                <small class="text-muted">{{ $vuelo->fecha_salida }}</small>
                            </div>

                            {{-- Ruta visual --}}
                            <div class="col-md-3 text-center px-0">
                                <small class="text-muted d-block mb-1">{{ $duracionTotal }}</small>
                                @if($tieneEscalas)
                                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-1">
                                        @foreach($subTramos as $st)
                                            <span class="text-muted" style="font-size:.75rem">
                                                <i class="bi bi-arrow-right text-warning"></i>
                                            </span>
                                            <span class="badge bg-warning text-dark" style="font-size:.72rem"
                                                  title="{{ $st->aeropuertoDestino->ciudad }}{{ $st->tiempo_escala ? ' · escala ' . substr($st->tiempo_escala,0,5) : '' }}">
                                                {{ $st->aeropuertoDestino->codigo_IATA }}
                                                @if($st->tiempo_escala && !$loop->last)
                                                    <span class="opacity-75">({{ substr($st->tiempo_escala,0,5) }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                        <span class="text-muted" style="font-size:.75rem">
                                            <i class="bi bi-arrow-right text-warning"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size:.68rem">
                                        @foreach($subTramos->filter(fn($s) => $s->tiempo_escala && !$loop->last) as $st)
                                            Escala {{ $st->aeropuertoDestino->codigo_IATA }}: {{ substr($st->tiempo_escala,0,5) }}
                                        @endforeach
                                    </small>
                                @else
                                    <i class="bi bi-arrow-right text-primary fs-4"></i>
                                @endif
                            </div>

                            {{-- Destino --}}
                            <div class="col-md-2 text-center">
                                <div class="fw-bold fs-5">{{ $vuelo->aeropuertoDestino->codigo_IATA }}</div>
                                <small class="text-muted">{{ $vuelo->aeropuertoDestino->ciudad }}</small>
                                <div class="text-primary fw-semibold">{{ substr($vuelo->hora_llegada, 0, 5) }}</div>
                                <small class="text-muted">{{ $vuelo->fecha_llegada }}</small>
                            </div>

                            <div class="col-md-1 text-center">
                                <div class="fw-bold text-success">Bs. {{ number_format($precioMinimo, 2) }}</div>
                                <small class="text-muted d-block">desde</small>
                                <small class="text-muted">{{ $vuelo->asientos_disponibles }} asiento(s)</small>
                            </div>

                            <div class="col-md-2 text-center d-flex flex-column gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modal-vuelo-{{ $vuelo->id }}">
                                    <i class="bi bi-info-circle"></i> Ver detalle
                                </button>
                                @if($vuelo->asientos_disponibles > 0)
                                    <a href="{{ route('cliente.seleccionar.asiento', $vuelo) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Seleccionar
                                    </a>
                                @else
                                    <span class="badge bg-danger">Agotado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal detalle vuelo --}}
                <div class="modal fade" id="modal-vuelo-{{ $vuelo->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-airplane"></i> {{ $vuelo->codigo_vuelo }}
                                    — {{ $vuelo->aeropuertoOrigen->codigo_IATA }} → {{ $vuelo->aeropuertoDestino->codigo_IATA }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="bi bi-calendar me-1"></i>{{ $vuelo->fecha_salida }}</span>
                                    <span><i class="bi bi-clock me-1"></i>Duración total: <strong>{{ $duracionTotal }}</strong></span>
                                </div>

                                @if($tieneEscalas)
                                {{-- Timeline de escalas --}}
                                <div class="position-relative ps-3">
                                    {{-- Línea vertical --}}
                                    <div class="position-absolute start-0 top-0 bottom-0" style="width:2px;background:#dee2e6;left:11px"></div>

                                    @foreach($subTramos as $st)
                                    <div class="d-flex gap-3 mb-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1 bg-light rounded p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="fw-bold">{{ $st->aeropuertoOrigen->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $st->aeropuertoOrigen->ciudad }}</span>
                                                    <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                    <span class="fw-bold">{{ $st->aeropuertoDestino->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $st->aeropuertoDestino->ciudad }}</span>
                                                </div>
                                                <span class="badge bg-primary ms-2">
                                                    <i class="bi bi-airplane me-1"></i>{{ substr($st->duracion_estimada, 0, 5) }}
                                                </span>
                                            </div>
                                            @if($st->tiempo_escala && !$loop->last)
                                            <div class="mt-1 d-flex align-items-center gap-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>Escala en {{ $st->aeropuertoDestino->ciudad }}: {{ substr($st->tiempo_escala, 0, 5) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    {{-- Destino final --}}
                                    <div class="d-flex gap-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1">
                                            <i class="bi bi-geo-alt-fill" style="font-size:.6rem"></i>
                                        </div>
                                        <div class="text-success fw-semibold small pt-1">
                                            Llegada: {{ $vuelo->aeropuertoDestino->codigo_IATA }} — {{ $vuelo->aeropuertoDestino->ciudad }}
                                            · {{ substr($vuelo->hora_llegada, 0, 5) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Resumen precios por clase --}}
                                @if($vuelo->precios->count() > 0)
                                <hr>
                                <div class="row g-2 mt-1">
                                    @foreach($vuelo->precios->sortBy('precio') as $precio)
                                    <div class="col-auto">
                                        <span class="badge bg-light text-dark border">
                                            {{ $precio->tipoClase->nombre ?? '—' }}: <strong>Bs. {{ number_format($precio->precio, 2) }}</strong>
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-arrow-right text-success fs-3"></i>
                                    <p class="text-muted mt-2 mb-1">Vuelo directo sin escalas</p>
                                    <small class="text-muted">
                                        {{ $vuelo->aeropuertoOrigen->ciudad }} → {{ $vuelo->aeropuertoDestino->ciudad }}
                                        · {{ substr($vuelo->hora_salida, 0, 5) }} — {{ substr($vuelo->hora_llegada, 0, 5) }}
                                    </small>
                                    @if($vuelo->precios->count() > 0)
                                    <div class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                                        @foreach($vuelo->precios->sortBy('precio') as $precio)
                                        <span class="badge bg-light text-dark border">
                                            {{ $precio->tipoClase->nombre ?? '—' }}: <strong>Bs. {{ number_format($precio->precio, 2) }}</strong>
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                @if($vuelo->asientos_disponibles > 0)
                                <a href="{{ route('cliente.seleccionar.asiento', $vuelo) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Seleccionar asiento
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif

            {{-- Tramos parciales --}}
            @if(($resultadosParciales ?? collect())->count() > 0)
                <p class="text-muted small fw-semibold mb-2 mt-3"><i class="bi bi-sign-intersection-fill me-1"></i>Tramo parcial (con escala)</p>
                @foreach($resultadosParciales as $ri => $r)
                @php
                    $prog = $r['programacion'];
                    $st   = $r['sub_tramo'];
                    $todosSubTramos = $prog->rutaTramo?->tramo?->subTramos->sortBy('orden') ?? collect();
                    $precioMinimoP  = $prog->precios->min('precio') ?? $prog->precio_base;
                @endphp
                <div class="card shadow-sm mb-3 border-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <h4 class="text-primary mb-0">{{ $prog->codigo_vuelo }}</h4>
                                <small class="badge bg-warning text-dark">Tramo parcial</small>
                                <div class="text-muted mt-1" style="font-size:.68rem">
                                    <i class="bi bi-info-circle me-1"></i>Vuelo completo:<br>
                                    {{ $prog->aeropuertoOrigen->codigo_IATA }} → {{ $prog->aeropuertoDestino->codigo_IATA }}
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="fw-bold fs-5">{{ $st->aeropuertoOrigen->codigo_IATA }}</div>
                                <small class="text-muted">{{ $st->aeropuertoOrigen->ciudad }}</small>
                                <div class="text-primary fw-semibold">{{ substr($prog->hora_salida, 0, 5) }}</div>
                                <small class="text-muted">{{ $prog->fecha_salida }}</small>
                            </div>
                            <div class="col-md-3 text-center px-0">
                                <small class="text-muted d-block mb-1">{{ substr($st->duracion_estimada, 0, 5) }}</small>
                                <i class="bi bi-arrow-right text-warning fs-4"></i>
                                <small class="d-block text-warning mt-1" style="font-size:.7rem">
                                    <i class="bi bi-sign-intersection-fill me-1"></i>Solo este tramo
                                </small>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="fw-bold fs-5">{{ $st->aeropuertoDestino->codigo_IATA }}</div>
                                <small class="text-muted">{{ $st->aeropuertoDestino->ciudad }}</small>
                            </div>
                            <div class="col-md-1 text-center">
                                <div class="fw-bold text-success">Bs. {{ number_format($precioMinimoP, 2) }}</div>
                                <small class="text-muted d-block">desde</small>
                                <small class="text-muted">{{ $prog->asientos_disponibles }} asiento(s)</small>
                            </div>
                            <div class="col-md-2 text-center d-flex flex-column gap-1">
                                <button type="button" class="btn btn-outline-warning btn-sm text-dark"
                                        data-bs-toggle="modal" data-bs-target="#modal-parcial-{{ $ri }}">
                                    <i class="bi bi-info-circle"></i> Ver detalle
                                </button>
                                @if($prog->asientos_disponibles > 0)
                                    <a href="{{ route('cliente.seleccionar.asiento', $prog) }}?sub_tramo_id={{ $st->id }}"
                                       class="btn btn-warning btn-sm text-dark">
                                        <i class="bi bi-cart-plus"></i> Seleccionar
                                    </a>
                                @else
                                    <span class="badge bg-danger">Agotado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal detalle tramo parcial --}}
                <div class="modal fade" id="modal-parcial-{{ $ri }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title">
                                    <i class="bi bi-sign-intersection-fill"></i>
                                    Tramo parcial — {{ $st->aeropuertoOrigen->codigo_IATA }} → {{ $st->aeropuertoDestino->codigo_IATA }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning py-2 mb-3">
                                    <small>
                                        <i class="bi bi-info-circle me-1"></i>
                                        Está reservando <strong>solo el tramo
                                        {{ $st->aeropuertoOrigen->codigo_IATA }} → {{ $st->aeropuertoDestino->codigo_IATA }}</strong>
                                        del vuelo <strong>{{ $prog->codigo_vuelo }}</strong>
                                        ({{ $prog->aeropuertoOrigen->codigo_IATA }} → {{ $prog->aeropuertoDestino->codigo_IATA }}).
                                    </small>
                                </div>

                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="bi bi-calendar me-1"></i>{{ $prog->fecha_salida }}</span>
                                    <span><i class="bi bi-airplane me-1"></i>Vuelo {{ $prog->codigo_vuelo }}</span>
                                </div>

                                {{-- Timeline completa del vuelo, resaltando el tramo elegido --}}
                                <p class="text-muted small fw-semibold mb-2">Ruta completa del vuelo:</p>
                                <div class="position-relative ps-3">
                                    <div class="position-absolute start-0 top-0 bottom-0" style="width:2px;background:#dee2e6;left:11px"></div>

                                    @foreach($todosSubTramos as $tramo)
                                    @php $esElegido = $tramo->id === $st->id; @endphp
                                    <div class="d-flex gap-3 mb-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1;background:{{ $esElegido ? '#ffc107' : '#6c757d' }};color:{{ $esElegido ? '#000' : '#fff' }}!important">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1 rounded p-2 {{ $esElegido ? 'bg-warning bg-opacity-25 border border-warning' : 'bg-light' }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="fw-bold">{{ $tramo->aeropuertoOrigen->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $tramo->aeropuertoOrigen->ciudad }}</span>
                                                    <i class="bi bi-arrow-right mx-1 {{ $esElegido ? 'text-warning' : 'text-primary' }}"></i>
                                                    <span class="fw-bold">{{ $tramo->aeropuertoDestino->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $tramo->aeropuertoDestino->ciudad }}</span>
                                                </div>
                                                <span class="badge {{ $esElegido ? 'bg-warning text-dark' : 'bg-secondary' }} ms-2">
                                                    <i class="bi bi-airplane me-1"></i>{{ substr($tramo->duracion_estimada, 0, 5) }}
                                                </span>
                                            </div>
                                            @if($esElegido)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-check-circle me-1"></i>Su tramo seleccionado
                                                </span>
                                            </div>
                                            @endif
                                            @if($tramo->tiempo_escala && !$loop->last)
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-hourglass-split me-1"></i>Escala en {{ $tramo->aeropuertoDestino->ciudad }}: {{ substr($tramo->tiempo_escala, 0, 5) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    <div class="d-flex gap-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1">
                                            <i class="bi bi-geo-alt-fill" style="font-size:.6rem"></i>
                                        </div>
                                        <div class="text-success fw-semibold small pt-1">
                                            Destino final del vuelo: {{ $prog->aeropuertoDestino->codigo_IATA }} — {{ $prog->aeropuertoDestino->ciudad }}
                                        </div>
                                    </div>
                                </div>

                                @if($prog->precios->count() > 0)
                                <hr>
                                <div class="row g-2">
                                    @foreach($prog->precios->sortBy('precio') as $precio)
                                    <div class="col-auto">
                                        <span class="badge bg-light text-dark border">
                                            {{ $precio->tipoClase->nombre ?? '—' }}: <strong>Bs. {{ number_format($precio->precio, 2) }}</strong>
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                @if($prog->asientos_disponibles > 0)
                                <a href="{{ route('cliente.seleccionar.asiento', $prog) }}?sub_tramo_id={{ $st->id }}"
                                   class="btn btn-warning btn-sm text-dark">
                                    <i class="bi bi-cart-plus"></i> Seleccionar este tramo
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif

        @else
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> No se encontraron vuelos disponibles para la fecha y ruta seleccionada.
            </div>
        @endif
        @endisset
    </div>
</div>
@endsection