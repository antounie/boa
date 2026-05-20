<!DOCTYPE html>
<html lang="es" data-tema="adultos" data-modo="dia">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BoA - Boliviana de Aviación</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/temas.css') }}">
    <style>
        /* Hero usa el mismo gradiente del navbar del tema activo */
        .hero-section {
            background: var(--nav-bg);
            min-height: 440px;
            display: flex;
            align-items: center;
            padding: 70px 0 50px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .hero-planes {
            position: absolute;
            right: -20px;
            bottom: 20px;
            font-size: 9rem;
            opacity: 0.06;
            transform: rotate(-20deg);
            pointer-events: none;
        }

        .search-card {
            border-radius: var(--card-radius);
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
            border: none !important;
        }

        /* Barra de vuelo resultado */
        .vuelo-card {
            border-left: 4px solid var(--accent);
            border-radius: var(--card-radius);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .vuelo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
        }

        /* Ruta de vuelo visual */
        .ruta-linea {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ruta-linea::before {
            content: '';
            position: absolute;
            left: 0; right: 0;
            top: 50%;
            height: 2px;
            background: linear-gradient(90deg, var(--border-color), var(--accent), var(--border-color));
        }

        .ruta-linea i {
            position: relative;
            z-index: 1;
            background: var(--card-bg);
            padding: 0 6px;
            color: var(--accent);
        }

        /* Destinos */
        .destino-card {
            border-radius: var(--card-radius);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: default;
            border: none !important;
        }

        .destino-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 32px rgba(0,0,0,0.18) !important;
        }

        .destino-banner {
            height: 140px;
            display: flex;
            align-items: flex-end;
            padding: 16px;
            position: relative;
            overflow: hidden;
        }

        .destino-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 60%);
        }

        .destino-banner > * {
            position: relative;
            z-index: 1;
        }

        /* Features */
        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 14px;
            transition: transform 0.3s;
        }

        .feature-icon:hover {
            transform: scale(1.1) rotate(5deg);
        }

        /* Sección con fondo alternado */
        .section-alt {
            background-color: var(--table-stripe);
        }

        /* Precio destacado */
        .precio-vuelo {
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: 700;
        }

        /* Badge de asientos */
        .asientos-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: var(--btn-radius);
        }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('welcome') }}">
            <i class="bi bi-airplane-fill me-2"></i>BoA
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#destinos">
                        <i class="bi bi-geo-alt me-1"></i>Destinos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#por-que-boa">
                        <i class="bi bi-info-circle me-1"></i>Nosotros
                    </a>
                </li>
            </ul>

            {{-- Selector de tema --}}
            <div class="tema-selector me-3">
                <button class="tema-btn tema-adultos" data-tema="adultos" title="Profesional"></button>
                <button class="tema-btn tema-ninos"   data-tema="ninos"   title="Familiar"></button>
                <button class="tema-btn tema-jovenes" data-tema="jovenes" title="Moderno"></button>
                <span class="modo-toggle ms-1" id="modoToggle" title="Modo día/noche">
                    <i class="bi bi-sun-fill" id="modoIcono"></i>
                </span>
            </div>

            {{-- Acciones de usuario --}}
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('cliente.dashboard') }}" class="btn btn-light btn-sm fw-semibold">
                        <i class="bi bi-person-circle me-1"></i>Mi cuenta
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm fw-semibold">
                        <i class="bi bi-person-plus me-1"></i>Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Hero + Buscador --}}
<section class="hero-section">
    <i class="bi bi-airplane hero-planes"></i>
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-5 text-white">
                <p class="text-white-50 fw-semibold mb-2 small text-uppercase letter-spacing-1">
                    <i class="bi bi-globe me-1"></i>La aerolínea de Bolivia
                </p>
                <h1 class="display-5 fw-bold mb-3 lh-sm">
                    Tu vuelo,<br>tu aventura
                </h1>
                <p class="lead mb-4 opacity-75">
                    Conectamos Bolivia con el mundo. Reserva tu pasaje de forma rápida y segura.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="text-center">
                        <div class="fw-bold fs-4">15+</div>
                        <div class="small opacity-75">Destinos</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4">50K+</div>
                        <div class="small opacity-75">Pasajeros/año</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4">99%</div>
                        <div class="small opacity-75">Puntualidad</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card search-card p-4">
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show mb-3 py-2" role="alert">
                            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span class="feature-icon d-inline-flex" style="width:36px;height:36px;font-size:1rem;background:var(--dropdown-hover);">
                            <i class="bi bi-search" style="color:var(--accent)"></i>
                        </span>
                        Buscar vuelos disponibles
                    </h5>
                    <form action="{{ route('welcome.buscar') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-geo-alt me-1" style="color:var(--accent)"></i>Origen
                                </label>
                                <select name="origen" class="form-select @error('origen') is-invalid @enderror" required>
                                    <option value="">Seleccione ciudad de origen</option>
                                    @foreach($aeropuertos as $ap)
                                        <option value="{{ $ap->id }}" {{ old('origen') == $ap->id ? 'selected' : '' }}>
                                            {{ $ap->ciudad }} — {{ $ap->codigo_IATA }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-geo-alt-fill me-1" style="color:var(--accent)"></i>Destino
                                </label>
                                <select name="destino" class="form-select @error('destino') is-invalid @enderror" required>
                                    <option value="">Seleccione ciudad de destino</option>
                                    @foreach($aeropuertos as $ap)
                                        <option value="{{ $ap->id }}" {{ old('destino') == $ap->id ? 'selected' : '' }}>
                                            {{ $ap->ciudad }} — {{ $ap->codigo_IATA }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destino')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-calendar3 me-1" style="color:var(--accent)"></i>Fecha de salida
                                </label>
                                <input type="date" name="fecha"
                                    class="form-control @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                                    <i class="bi bi-search me-2"></i>Buscar vuelos
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Resultados de búsqueda --}}
@isset($vuelos)
<section class="py-5 section-alt">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-4">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-airplane me-2" style="color:var(--accent)"></i>Vuelos disponibles
            </h2>
            <span class="badge bg-primary fs-6">{{ $vuelos->count() + $resultadosParciales->count() }}</span>
        </div>

        @if($vuelos->isEmpty() && $resultadosParciales->isEmpty())
            <div class="alert alert-warning d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle fs-4"></i>
                <div>
                    No se encontraron vuelos para la ruta y fecha seleccionadas. Prueba con otra fecha.
                </div>
            </div>
        @else

            {{-- Vuelos de ruta completa --}}
            @if($vuelos->isNotEmpty())
            <h5 class="fw-semibold mb-3 text-muted">
                <i class="bi bi-arrow-right-circle me-1" style="color:var(--accent)"></i>Vuelo directo
            </h5>
            <div class="row g-3 mb-4">
                @foreach($vuelos as $vuelo)
                @php
                    $subTramos    = $vuelo->rutaTramo?->tramo?->subTramos->sortBy('orden') ?? collect();
                    $tieneEscalas = $subTramos->count() > 0;
                    $disp         = $vuelo->asientos_disponibles ?? 0;
                    $precioMin    = $vuelo->precios->min('precio') ?? $vuelo->precio_base;
                    $duracionTotal = $vuelo->rutaTramo?->tramo?->duracion_estimada;
                @endphp
                <div class="col-12">
                    <div class="card vuelo-card shadow-sm">
                        <div class="card-body py-3">
                            <div class="row align-items-center g-3">
                                {{-- Código y tipo --}}
                                <div class="col-md-2">
                                    <div class="text-muted small mb-1">Vuelo</div>
                                    <div class="fw-bold fs-5">{{ $vuelo->codigo_vuelo }}</div>
                                    @if($tieneEscalas)
                                        <span class="badge bg-warning text-dark" style="font-size:0.7rem">{{ $subTramos->count() }} escala(s)</span>
                                    @else
                                        <span class="badge bg-success" style="font-size:0.7rem">Directo</span>
                                    @endif
                                    @if($duracionTotal)
                                        <div class="text-muted mt-1" style="font-size:.68rem"><i class="bi bi-clock me-1"></i>{{ substr($duracionTotal,0,5) }}</div>
                                    @endif
                                </div>

                                {{-- Ruta con escalas intermedias --}}
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-center flex-shrink-0">
                                            <div class="fw-bold fs-4">{{ \Carbon\Carbon::parse($vuelo->hora_salida)->format('H:i') }}</div>
                                            <div class="badge" style="background:var(--accent);font-size:0.75rem;">{{ $vuelo->aeropuertoOrigen->codigo_IATA }}</div>
                                            <div class="text-muted mt-1" style="font-size:0.7rem;">{{ $vuelo->aeropuertoOrigen->ciudad }}</div>
                                        </div>

                                        @if($tieneEscalas)
                                            {{-- Mostrar paradas intermedias --}}
                                            @foreach($subTramos as $st)
                                            <div class="ruta-linea" style="min-width:20px"></div>
                                            <div class="text-center flex-shrink-0">
                                                <div class="badge bg-warning text-dark" style="font-size:.72rem">{{ $st->aeropuertoDestino->codigo_IATA }}</div>
                                                @if($st->tiempo_escala && !$loop->last)
                                                <div style="font-size:.62rem;color:#856404;white-space:nowrap">
                                                    <i class="bi bi-hourglass-split"></i> {{ substr($st->tiempo_escala,0,5) }}
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                            <div class="ruta-linea" style="min-width:20px"><i class="bi bi-airplane fs-5"></i></div>
                                        @else
                                            <div class="ruta-linea"><i class="bi bi-airplane fs-5"></i></div>
                                        @endif

                                        <div class="text-center flex-shrink-0">
                                            <div class="fw-bold fs-4">{{ \Carbon\Carbon::parse($vuelo->hora_llegada)->format('H:i') }}</div>
                                            <div class="badge" style="background:var(--accent);font-size:0.75rem;">{{ $vuelo->aeropuertoDestino->codigo_IATA }}</div>
                                            <div class="text-muted mt-1" style="font-size:0.7rem;">{{ $vuelo->aeropuertoDestino->ciudad }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1 text-center d-none d-md-block">
                                    <div class="text-muted small">Fecha</div>
                                    <div class="fw-semibold small">{{ \Carbon\Carbon::parse($vuelo->fecha_salida)->format('d M') }}</div>
                                </div>

                                <div class="col-md-2 text-center">
                                    <div class="text-muted small mb-1">Asientos</div>
                                    <span class="asientos-badge {{ $disp > 5 ? 'bg-success' : ($disp > 0 ? 'bg-warning text-dark' : 'bg-danger') }} text-white">
                                        @if($disp > 0)
                                            <i class="bi bi-chair me-1"></i>{{ $disp }}
                                        @else
                                            <i class="bi bi-x-circle me-1"></i>Agotado
                                        @endif
                                    </span>
                                </div>

                                <div class="col-md-2 text-end">
                                    <div class="text-muted small mb-1">Desde</div>
                                    <div class="precio-vuelo mb-1">Bs. {{ number_format($precioMin, 2) }}</div>
                                    @if($tieneEscalas)
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-1 w-100"
                                            data-bs-toggle="modal" data-bs-target="#modal-w-{{ $vuelo->id }}">
                                        <i class="bi bi-info-circle me-1"></i>Ver escala
                                    </button>
                                    @endif
                                    @if($disp > 0)
                                        <a href="{{ route('welcome.seleccionar', $vuelo) }}" class="btn btn-primary btn-sm fw-semibold px-3 w-100">
                                            Seleccionar <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>Agotado</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal detalle escalas --}}
                @if($tieneEscalas)
                <div class="modal fade" id="modal-w-{{ $vuelo->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background:var(--accent);color:#fff">
                                <h5 class="modal-title">
                                    <i class="bi bi-airplane me-2"></i>{{ $vuelo->codigo_vuelo }}
                                    — {{ $vuelo->aeropuertoOrigen->codigo_IATA }} → {{ $vuelo->aeropuertoDestino->codigo_IATA }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="bi bi-calendar me-1"></i>{{ $vuelo->fecha_salida }}</span>
                                    @if($duracionTotal)
                                    <span><i class="bi bi-clock me-1"></i>Duración total: <strong>{{ substr($duracionTotal,0,5) }}</strong></span>
                                    @endif
                                </div>

                                <div class="position-relative ps-3">
                                    <div class="position-absolute start-0 top-0 bottom-0" style="width:2px;background:#dee2e6;left:11px"></div>
                                    @foreach($subTramos as $st)
                                    <div class="d-flex gap-3 mb-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle text-white"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1;background:var(--accent)">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1 bg-light rounded p-2">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                                <div>
                                                    <span class="fw-bold">{{ $st->aeropuertoOrigen->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $st->aeropuertoOrigen->ciudad }}</span>
                                                    <i class="bi bi-arrow-right mx-1" style="color:var(--accent)"></i>
                                                    <span class="fw-bold">{{ $st->aeropuertoDestino->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $st->aeropuertoDestino->ciudad }}</span>
                                                </div>
                                                <span class="badge" style="background:var(--accent)">
                                                    <i class="bi bi-airplane me-1"></i>{{ substr($st->duracion_estimada,0,5) }}
                                                </span>
                                            </div>
                                            @if($st->tiempo_escala && !$loop->last)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>Escala en {{ $st->aeropuertoDestino->ciudad }}: {{ substr($st->tiempo_escala,0,5) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="d-flex gap-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                             style="width:22px;height:22px;font-size:.6rem;z-index:1">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="text-success fw-semibold small pt-1">
                                            Llegada: {{ $vuelo->aeropuertoDestino->codigo_IATA }} — {{ $vuelo->aeropuertoDestino->ciudad }}
                                            · {{ \Carbon\Carbon::parse($vuelo->hora_llegada)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>

                                @if($vuelo->precios->count() > 0)
                                <hr>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($vuelo->precios->sortBy('precio') as $precio)
                                    <span class="badge bg-light text-dark border">
                                        {{ $precio->tipoClase->nombre ?? '—' }}: <strong>Bs. {{ number_format($precio->precio, 2) }}</strong>
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                @if($disp > 0)
                                <a href="{{ route('welcome.seleccionar', $vuelo) }}" class="btn btn-primary btn-sm fw-semibold">
                                    Seleccionar asiento <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Vuelos con tramo parcial --}}
            @if($resultadosParciales->isNotEmpty())
            <h5 class="fw-semibold mb-3 text-muted">
                <i class="bi bi-signpost-split me-1" style="color:var(--accent)"></i>Tramo parcial (con escala)
            </h5>
            <div class="row g-3">
                @foreach($resultadosParciales as $ri => $item)
                @php
                    $vuelo         = $item['programacion'];
                    $st            = $item['sub_tramo'];
                    $disp          = $vuelo->asientos_disponibles ?? 0;
                    $precioMinP    = $vuelo->precios->min('precio') ?? $vuelo->precio_base;
                    $todosSubTramos = $vuelo->rutaTramo?->tramo?->subTramos->sortBy('orden') ?? collect();
                @endphp
                <div class="col-12">
                    <div class="card vuelo-card shadow-sm" style="border-left-color:#fd7e14;">
                        <div class="card-body py-3">
                            <div class="row align-items-center g-3">
                                <div class="col-md-2">
                                    <div class="text-muted small mb-1">Vuelo</div>
                                    <div class="fw-bold fs-5">{{ $vuelo->codigo_vuelo }}</div>
                                    <span class="badge bg-warning text-dark" style="font-size:0.7rem">Tramo parcial</span>
                                    <div class="text-muted mt-1" style="font-size:.65rem">
                                        Vuelo completo:<br>{{ $vuelo->aeropuertoOrigen->codigo_IATA }} → {{ $vuelo->aeropuertoDestino->codigo_IATA }}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-center flex-shrink-0">
                                            <div class="fw-bold fs-4">{{ \Carbon\Carbon::parse($vuelo->hora_salida)->format('H:i') }}</div>
                                            <div class="badge" style="background:#fd7e14;font-size:0.75rem;">{{ $st->aeropuertoOrigen->codigo_IATA }}</div>
                                            <div class="text-muted mt-1" style="font-size:0.7rem;">{{ $st->aeropuertoOrigen->ciudad }}</div>
                                        </div>
                                        <div class="ruta-linea"><i class="bi bi-airplane fs-5" style="color:#fd7e14"></i></div>
                                        <div class="text-center flex-shrink-0">
                                            <div class="fw-bold fs-4">—</div>
                                            <div class="badge" style="background:#fd7e14;font-size:0.75rem;">{{ $st->aeropuertoDestino->codigo_IATA }}</div>
                                            <div class="text-muted mt-1" style="font-size:0.7rem;">{{ $st->aeropuertoDestino->ciudad }}</div>
                                        </div>
                                    </div>
                                    @if($st->duracion_estimada)
                                    <div class="text-muted mt-1" style="font-size:.7rem">
                                        <i class="bi bi-clock me-1"></i>Duración del tramo: {{ substr($st->duracion_estimada,0,5) }}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-1 text-center d-none d-md-block">
                                    <div class="text-muted small">Fecha</div>
                                    <div class="fw-semibold small">{{ \Carbon\Carbon::parse($vuelo->fecha_salida)->format('d M') }}</div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="text-muted small mb-1">Asientos</div>
                                    <span class="asientos-badge {{ $disp > 5 ? 'bg-success' : ($disp > 0 ? 'bg-warning text-dark' : 'bg-danger') }} text-white">
                                        @if($disp > 0)
                                            <i class="bi bi-chair me-1"></i>{{ $disp }}
                                        @else
                                            <i class="bi bi-x-circle me-1"></i>Agotado
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="text-muted small mb-1">Desde</div>
                                    <div class="precio-vuelo mb-1">Bs. {{ number_format($precioMinP, 2) }}</div>
                                    <button type="button" class="btn btn-outline-warning btn-sm mb-1 w-100 text-dark"
                                            data-bs-toggle="modal" data-bs-target="#modal-wp-{{ $ri }}">
                                        <i class="bi bi-info-circle me-1"></i>Ver detalle
                                    </button>
                                    @if($disp > 0)
                                        <a href="{{ route('welcome.seleccionar', $vuelo) }}?sub_tramo_id={{ $st->id }}"
                                           class="btn btn-warning btn-sm fw-semibold px-3 text-dark w-100">
                                            Seleccionar <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>Agotado</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal detalle tramo parcial --}}
                <div class="modal fade" id="modal-wp-{{ $ri }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title">
                                    <i class="bi bi-signpost-split me-2"></i>Tramo {{ $st->aeropuertoOrigen->codigo_IATA }} → {{ $st->aeropuertoDestino->codigo_IATA }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning py-2 mb-3" style="font-size:.85rem">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Reserva <strong>solo el tramo {{ $st->aeropuertoOrigen->codigo_IATA }} → {{ $st->aeropuertoDestino->codigo_IATA }}</strong>
                                    del vuelo <strong>{{ $vuelo->codigo_vuelo }}</strong>
                                    (ruta completa: {{ $vuelo->aeropuertoOrigen->codigo_IATA }} → {{ $vuelo->aeropuertoDestino->codigo_IATA }}).
                                </div>

                                <p class="text-muted small fw-semibold mb-2">Ruta completa del vuelo:</p>
                                <div class="position-relative ps-3">
                                    <div class="position-absolute start-0 top-0 bottom-0" style="width:2px;background:#dee2e6;left:11px"></div>
                                    @foreach($todosSubTramos as $tramo)
                                    @php $esElegido = $tramo->id === $st->id; @endphp
                                    <div class="d-flex gap-3 mb-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                             style="width:22px;height:22px;font-size:.7rem;z-index:1;background:{{ $esElegido ? '#ffc107' : '#6c757d' }};color:{{ $esElegido ? '#000' : '#fff' }}">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1 rounded p-2 {{ $esElegido ? 'border border-warning' : 'bg-light' }}" style="{{ $esElegido ? 'background:rgba(255,193,7,.15)' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                                <div>
                                                    <span class="fw-bold">{{ $tramo->aeropuertoOrigen->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $tramo->aeropuertoOrigen->ciudad }}</span>
                                                    <i class="bi bi-arrow-right mx-1"></i>
                                                    <span class="fw-bold">{{ $tramo->aeropuertoDestino->codigo_IATA }}</span>
                                                    <span class="text-muted ms-1 small">{{ $tramo->aeropuertoDestino->ciudad }}</span>
                                                </div>
                                                <span class="badge {{ $esElegido ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                                    <i class="bi bi-airplane me-1"></i>{{ substr($tramo->duracion_estimada,0,5) }}
                                                </span>
                                            </div>
                                            @if($esElegido)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark"><i class="bi bi-check-circle me-1"></i>Su tramo</span>
                                            </div>
                                            @endif
                                            @if($tramo->tiempo_escala && !$loop->last)
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-hourglass-split me-1"></i>Escala en {{ $tramo->aeropuertoDestino->ciudad }}: {{ substr($tramo->tiempo_escala,0,5) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="d-flex gap-3 position-relative">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                             style="width:22px;height:22px;font-size:.6rem;z-index:1">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="text-success fw-semibold small pt-1">
                                            Destino final: {{ $vuelo->aeropuertoDestino->codigo_IATA }} — {{ $vuelo->aeropuertoDestino->ciudad }}
                                        </div>
                                    </div>
                                </div>

                                @if($vuelo->precios->count() > 0)
                                <hr>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($vuelo->precios->sortBy('precio') as $precio)
                                    <span class="badge bg-light text-dark border">
                                        {{ $precio->tipoClase->nombre ?? '—' }}: <strong>Bs. {{ number_format($precio->precio, 2) }}</strong>
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                @if($disp > 0)
                                <a href="{{ route('welcome.seleccionar', $vuelo) }}?sub_tramo_id={{ $st->id }}"
                                   class="btn btn-warning btn-sm fw-semibold text-dark">
                                    Seleccionar este tramo <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        @endif
    </div>
</section>
@endisset

{{-- Destinos populares --}}
<section class="py-5" id="destinos">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Destinos populares</h2>
            <p class="text-muted">Descubre los destinos más buscados de Bolivia</p>
        </div>
        @php
            $destinoGradients = [
                'linear-gradient(135deg,#0a1628,#1a5276)',
                'linear-gradient(135deg,#00695C,#26A69A)',
                'linear-gradient(135deg,#E65100,#FFA726)',
                'linear-gradient(135deg,#1565C0,#42A5F5)',
                'linear-gradient(135deg,#4A148C,#7B1FA2)',
                'linear-gradient(135deg,#1B5E20,#43A047)',
            ];
        @endphp
        <div class="row g-4">
            @foreach($aeropuertos->take(6) as $i => $ap)
            <div class="col-md-4 col-sm-6">
                <div class="card destino-card shadow-sm">
                    <div class="destino-banner" style="background: {{ $destinoGradients[$i % count($destinoGradients)] }};">
                        <div class="text-white">
                            <div class="fw-bold fs-5">{{ $ap->ciudad }}</div>
                            <div class="opacity-75 small">
                                <i class="bi bi-geo-alt me-1"></i>{{ $ap->codigo_iata }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-building me-1"></i>{{ $ap->nombre }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ¿Por qué volar con BoA? --}}
<section class="py-5 section-alt" id="por-que-boa">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">¿Por qué volar con BoA?</h2>
            <p class="text-muted">La aerolínea de Bolivia a tu servicio</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon mx-auto" style="background:var(--dropdown-hover);">
                    <i class="bi bi-shield-check" style="color:var(--accent)"></i>
                </div>
                <h6 class="fw-bold">Seguridad garantizada</h6>
                <p class="text-muted small">Flota moderna con estándares internacionales de mantenimiento.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon mx-auto" style="background:#d1fae5;">
                    <i class="bi bi-cash-coin text-success"></i>
                </div>
                <h6 class="fw-bold">Mejores precios</h6>
                <p class="text-muted small">Tarifas competitivas para todas las rutas nacionales.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon mx-auto" style="background:#fef3c7;">
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
                <h6 class="fw-bold">Puntualidad</h6>
                <p class="text-muted small">Comprometidos con la puntualidad en cada vuelo.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon mx-auto" style="background:#dbeafe;">
                    <i class="bi bi-headset text-info"></i>
                </div>
                <h6 class="fw-bold">Atención 24/7</h6>
                <p class="text-muted small">Soporte al pasajero disponible en todo momento.</p>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="py-4">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <h5 class="fw-bold mb-2 text-white">
                    <i class="bi bi-airplane-fill me-2"></i>BoA
                </h5>
                <p class="small mb-0" style="color:var(--footer-text);opacity:0.75;">
                    Boliviana de Aviación — La aerolínea del Estado Plurinacional de Bolivia.
                </p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2 text-white">Acceso rápido</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><a href="{{ route('welcome') }}"><i class="bi bi-house me-1"></i>Inicio</a></li>
                    <li class="mb-1"><a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión</a></li>
                    <li><a href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Registrarse</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2 text-white">Contacto</h6>
                <p class="small mb-0" style="color:var(--footer-text);opacity:0.75;">
                    <i class="bi bi-envelope me-1"></i>info@boa.bo<br>
                    <i class="bi bi-telephone me-1"></i>+591 76661807<br>
                    <i class="bi bi-geo-alt me-1"></i>La Paz, Bolivia
                </p>
            </div>
        </div>
        <hr class="my-3" style="border-color:rgba(255,255,255,0.15);">
        <p class="text-center small mb-0" style="color:var(--footer-text);opacity:0.6;">
            &copy; {{ date('Y') }} Boliviana de Aviación. Todos los derechos reservados.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const html = document.documentElement;

    // Aplicar tema guardado
    const temaGuardado = localStorage.getItem('boa_tema') || 'adultos';
    html.setAttribute('data-tema', temaGuardado);
    document.querySelectorAll('.tema-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-tema') === temaGuardado);
    });

    // Detectar y aplicar modo
    function detectarModo() {
        const guardado = localStorage.getItem('boa_modo');
        if (guardado) return guardado;
        const h = new Date().getHours();
        return (h >= 6 && h < 18) ? 'dia' : 'noche';
    }

    function aplicarModo(modo) {
        html.setAttribute('data-modo', modo);
        const icono = document.getElementById('modoIcono');
        if (!icono) return;
        if (modo === 'noche') {
            icono.classList.replace('bi-sun-fill', 'bi-moon-fill');
        } else {
            icono.classList.replace('bi-moon-fill', 'bi-sun-fill');
        }
    }

    aplicarModo(detectarModo());

    // Cambiar tema
    document.querySelectorAll('.tema-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const tema = this.getAttribute('data-tema');
            html.setAttribute('data-tema', tema);
            localStorage.setItem('boa_tema', tema);
            document.querySelectorAll('.tema-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Toggle modo día/noche
    document.getElementById('modoToggle')?.addEventListener('click', function () {
        const actual = html.getAttribute('data-modo');
        const nuevo = actual === 'dia' ? 'noche' : 'dia';
        localStorage.setItem('boa_modo', nuevo);
        aplicarModo(nuevo);
    });
</script>
</body>
</html>
