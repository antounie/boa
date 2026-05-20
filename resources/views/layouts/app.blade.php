<!DOCTYPE html>
<html lang="es" data-tema="adultos" data-modo="dia">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') - BoA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/temas.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-airplane-fill"></i> BoA - Sistema
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @yield('menu')
                </ul>
                <form class="d-flex me-3" action="{{ route('buscar.info') }}" method="GET" role="search">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="q" placeholder="Buscar..." value="{{ request('q') }}" style="width: 150px;">
                        <button class="btn btn-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                <ul class="navbar-nav align-items-center">
                    {{-- Usuario --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button"
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 220px;">
                            <li><span class="dropdown-item-text fw-semibold">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</span></li>
                            <li><span class="dropdown-item-text text-muted small">{{ Auth::user()->rol->nombre }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    <i class="bi bi-palette me-1"></i>Apariencia
                                </span>
                            </li>
                            <li>
                                <div class="px-3 py-1 d-flex align-items-center gap-2">
                                    <button class="tema-btn tema-adultos" data-tema="adultos" title="Adultos"></button>
                                    <button class="tema-btn tema-ninos"   data-tema="ninos"   title="Niños"></button>
                                    <button class="tema-btn tema-jovenes" data-tema="jovenes" title="Jóvenes"></button>
                                    <span class="ms-1 text-muted small">Color</span>
                                </div>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" id="modoToggle" type="button">
                                    <i class="bi bi-sun-fill" id="modoIcono"></i>
                                    <span id="modoTexto">Modo Día</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth
    <main class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('contenido')
    </main>
    <footer class="text-center py-3 mt-4">
        <p class="mb-0">&copy; 2026 Boliviana de Aviación (BoA) - Sistema de Información Web</p>
        <small class="footer-visits">
            <i class="bi bi-eye"></i> Visitas a esta página: <strong>{{ $contadorVisitas ?? 0 }}</strong>
        </small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script de Temas y Modo Día/Noche --}}
    <script>
        // Cargar tema guardado
        const temaGuardado = localStorage.getItem('boa_tema') || 'adultos';
        document.documentElement.setAttribute('data-tema', temaGuardado);
        document.querySelectorAll('.tema-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-tema') === temaGuardado);
        });

        // Modo automático día/noche según horario
        function detectarModo() {
            const modoGuardado = localStorage.getItem('boa_modo');
            if (modoGuardado) return modoGuardado;

            const hora = new Date().getHours();
            return (hora >= 6 && hora < 18) ? 'dia' : 'noche';
        }

        function aplicarModo(modo) {
            document.documentElement.setAttribute('data-modo', modo);
            const icono = document.getElementById('modoIcono');
            const texto = document.getElementById('modoTexto');
            if (icono) {
                if (modo === 'noche') {
                    icono.classList.replace('bi-sun-fill', 'bi-moon-fill');
                } else {
                    icono.classList.replace('bi-moon-fill', 'bi-sun-fill');
                }
            }
            if (texto) texto.textContent = modo === 'noche' ? 'Modo Noche' : 'Modo Día';
        }

        aplicarModo(detectarModo());

        // Cambiar tema al hacer clic
        document.querySelectorAll('.tema-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tema = this.getAttribute('data-tema');
                document.documentElement.setAttribute('data-tema', tema);
                localStorage.setItem('boa_tema', tema);
                document.querySelectorAll('.tema-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Toggle modo día/noche
        document.getElementById('modoToggle')?.addEventListener('click', function() {
            const modoActual = document.documentElement.getAttribute('data-modo');
            const nuevoModo = modoActual === 'dia' ? 'noche' : 'dia';
            localStorage.setItem('boa_modo', nuevoModo);
            aplicarModo(nuevoModo);
        });
    </script>

    @stack('scripts')
</body>
</html>
