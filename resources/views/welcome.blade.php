<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoA - Boliviana de Aviación</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/temas.css') }}">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #084298 100%);
            min-height: 420px;
            display: flex;
            align-items: center;
            padding: 60px 0 40px;
        }
        .search-card {
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .destino-card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: default;
        }
        .destino-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .destino-banner {
            height: 130px;
            display: flex;
            align-items: flex-end;
            padding: 14px;
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 12px;
        }
        .vuelo-card {
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
        }
        footer a {
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        footer a:hover { opacity: 1; }
    </style>
</head>
<body data-tema="{{ session('tema', 'adultos') }}" data-modo="{{ session('modo', 'dia') }}">

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('welcome') }}">
            <i class="bi bi-airplane-fill me-2"></i>BoA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#destinos">Destinos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#por-que-boa">Nosotros</a>
                </li>
            </ul>
            <div class="tema-selector me-3">
                <button class="tema-btn tema-adultos {{ session('tema','adultos') === 'adultos' ? 'active' : '' }}"
                    onclick="cambiarTema('adultos')" title="Tema Adultos"></button>
                <button class="tema-btn tema-ninos {{ session('tema','adultos') === 'ninos' ? 'active' : '' }}"
                    onclick="cambiarTema('ninos')" title="Tema Niños"></button>
                <button class="tema-btn tema-jovenes {{ session('tema','adultos') === 'jovenes' ? 'active' : '' }}"
                    onclick="cambiarTema('jovenes')" title="Tema Jóvenes"></button>
                <span class="modo-toggle ms-2" onclick="toggleModo()" title="Cambiar modo">
                    <i class="bi {{ session('modo','dia') === 'noche' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
                </span>
            </div>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('cliente.dashboard') }}" class="btn btn-light btn-sm fw-semibold">
                        <i class="bi bi-person-circle me-1"></i>Mi cuenta
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm fw-semibold">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Hero + Buscador --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5 text-white">
                <h1 class="display-5 fw-bold mb-3">Tu vuelo, tu aventura</h1>
                <p class="lead mb-0 opacity-90">
                    Conectamos Bolivia con el mundo. Reserva tu pasaje de forma rápida y segura.
                </p>
            </div>
            <div class="col-lg-7">
                <div class="card search-card border-0 p-4">
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-search me-2 text-primary"></i>Buscar vuelos
                    </h5>
                    <form action="{{ route('welcome.buscar') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Origen</label>
                                <select name="origen" class="form-select @error('origen') is-invalid @enderror" required>
                                    <option value="">Seleccione ciudad de origen</option>
                                    @foreach($aeropuertos as $ap)
                                        <option value="{{ $ap->id }}" {{ old('origen') == $ap->id ? 'selected' : '' }}>
                                            {{ $ap->ciudad }} ({{ $ap->codigo_iata }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('origen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Destino</label>
                                <select name="destino" class="form-select @error('destino') is-invalid @enderror" required>
                                    <option value="">Seleccione ciudad de destino</option>
                                    @foreach($aeropuertos as $ap)
                                        <option value="{{ $ap->id }}" {{ old('destino') == $ap->id ? 'selected' : '' }}>
                                            {{ $ap->ciudad }} ({{ $ap->codigo_iata }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('destino')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Fecha de salida</label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold">
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
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4">
            <i class="bi bi-airplane me-2 text-primary"></i>Vuelos disponibles
            <span class="badge bg-primary ms-2 fs-6">{{ $vuelos->count() }}</span>
        </h2>

        @if($vuelos->isEmpty())
            <div class="alert alert-warning d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle fs-4"></i>
                <div>
                    No se encontraron vuelos disponibles para la ruta y fecha seleccionadas.
                    Intente con otra fecha.
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($vuelos as $vuelo)
                <div class="col-12">
                    <div class="card vuelo-card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center g-3">
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Vuelo</div>
                                    <div class="fw-bold fs-5">{{ $vuelo->vuelo->numero_vuelo ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $vuelo->aeronave->modelo ?? '' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-center">
                                            <div class="fw-bold fs-5">{{ \Carbon\Carbon::parse($vuelo->hora_salida)->format('H:i') }}</div>
                                            <div class="text-muted small">{{ $vuelo->ruta->aeropuertoOrigen->codigo_iata ?? '' }}</div>
                                        </div>
                                        <div class="text-center flex-grow-1">
                                            <i class="bi bi-airplane text-primary fs-5"></i>
                                            <div class="border-top border-primary mt-1"></div>
                                        </div>
                                        <div class="text-center">
                                            <div class="fw-bold fs-5">{{ \Carbon\Carbon::parse($vuelo->hora_llegada)->format('H:i') }}</div>
                                            <div class="text-muted small">{{ $vuelo->ruta->aeropuertoDestino->codigo_iata ?? '' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="text-muted small mb-1">Disponibles</div>
                                    <span class="badge {{ $vuelo->asientos_disponibles > 5 ? 'bg-success' : ($vuelo->asientos_disponibles > 0 ? 'bg-warning text-dark' : 'bg-danger') }} fs-6">
                                        {{ $vuelo->asientos_disponibles }} asientos
                                    </span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="text-muted small mb-1">Desde</div>
                                    <div class="fw-bold fs-5 text-primary">Bs. {{ number_format($vuelo->precio_base, 2) }}</div>
                                </div>
                                <div class="col-md-1 text-end">
                                    @if($vuelo->asientos_disponibles > 0)
                                        <a href="{{ route('welcome.seleccionar', $vuelo) }}" class="btn btn-primary btn-sm">
                                            Seleccionar
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>Agotado</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
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
            $colores = [
                'linear-gradient(135deg,#0d6efd,#084298)',
                'linear-gradient(135deg,#20c997,#0f8060)',
                'linear-gradient(135deg,#fd7e14,#c0550a)',
                'linear-gradient(135deg,#6C5CE7,#4a3eaf)',
                'linear-gradient(135deg,#e83e8c,#b02a6b)',
                'linear-gradient(135deg,#17a2b8,#0e6879)',
            ];
        @endphp
        <div class="row g-4">
            @foreach($aeropuertos->take(6) as $i => $ap)
            <div class="col-md-4 col-sm-6">
                <div class="card destino-card border-0 shadow-sm">
                    <div class="destino-banner" style="background: {{ $colores[$i % count($colores)] }};">
                        <div class="text-white">
                            <div class="fw-bold fs-5">{{ $ap->ciudad }}</div>
                            <div class="opacity-75 small">{{ $ap->codigo_iata }}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-geo-alt me-1"></i>{{ $ap->nombre }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Por qué volar con BoA --}}
<section class="py-5 bg-light" id="por-que-boa">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">¿Por qué volar con BoA?</h2>
            <p class="text-muted">La aerolínea de bandera de Bolivia a tu servicio</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h6 class="fw-bold">Seguridad garantizada</h6>
                <p class="text-muted small">Flota moderna y mantenimiento de estándares internacionales.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <h6 class="fw-bold">Mejores precios</h6>
                <p class="text-muted small">Tarifas competitivas para todas las rutas nacionales.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h6 class="fw-bold">Puntualidad</h6>
                <p class="text-muted small">Comprometidos con la puntualidad en cada vuelo.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-headset"></i>
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
                <h5 class="fw-bold mb-2">
                    <i class="bi bi-airplane-fill me-2"></i>BoA
                </h5>
                <p class="small mb-0 opacity-75">
                    Boliviana de Aviación — La aerolínea de bandera del Estado Plurinacional de Bolivia.
                </p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2">Acceso rápido</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a href="{{ route('welcome') }}" class="text-reset">Inicio</a></li>
                    <li><a href="{{ route('login') }}" class="text-reset">Iniciar sesión</a></li>
                    <li><a href="{{ route('register') }}" class="text-reset">Registrarse</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-2">Contacto</h6>
                <p class="small mb-0 opacity-75">
                    <i class="bi bi-envelope me-1"></i>info@boa.bo<br>
                    <i class="bi bi-telephone me-1"></i>+591 2 2901234
                </p>
            </div>
        </div>
        <hr class="my-3 opacity-25">
        <p class="text-center small mb-0 opacity-75">
            &copy; {{ date('Y') }} Boliviana de Aviación. Todos los derechos reservados.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function cambiarTema(tema) {
    fetch('/tema/' + tema, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}' } })
        .then(() => { document.body.setAttribute('data-tema', tema); document.querySelectorAll('.tema-btn').forEach(b => b.classList.remove('active')); document.querySelector('.tema-' + tema)?.classList.add('active'); });
}
function toggleModo() {
    const actual = document.body.getAttribute('data-modo') || 'dia';
    const nuevo = actual === 'dia' ? 'noche' : 'dia';
    fetch('/modo/' + nuevo, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(() => { document.body.setAttribute('data-modo', nuevo); const icon = document.querySelector('.modo-toggle i'); if (icon) { icon.className = nuevo === 'noche' ? 'bi bi-sun-fill' : 'bi bi-moon-fill'; } });
}
</script>
</body>
</html>
