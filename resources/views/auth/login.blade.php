@extends('layouts.app')

@section('titulo', 'Iniciar Sesión')

@section('contenido')
<div class="auth-wrapper">
    <div class="w-100" style="max-width: 860px;">
        <div class="shadow-lg overflow-hidden" style="border-radius:var(--card-radius)">
            <div class="row g-0">

                {{-- Panel de marca --}}
                <div class="col-md-5 auth-brand-panel">
                    <div class="position-relative z-1">
                        <div class="mb-4">
                            <i class="bi bi-airplane-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.9)"></i>
                        </div>
                        <h1 class="fw-bold mb-2" style="font-size:2rem">BoA</h1>
                        <p class="mb-4" style="color:rgba(255,255,255,0.8);font-size:1rem">
                            Boliviana de Aviación
                        </p>
                        <hr style="border-color:rgba(255,255,255,0.2)">
                        <p class="small mt-3" style="color:rgba(255,255,255,0.7);line-height:1.6">
                            Conectamos Bolivia con el mundo. Accede a tu cuenta para gestionar reservas, tickets y más.
                        </p>
                        <div class="mt-4 d-flex gap-3">
                            <div class="text-center">
                                <div class="fw-bold fs-5">15+</div>
                                <div class="small" style="color:rgba(255,255,255,0.65)">Destinos</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5">24/7</div>
                                <div class="small" style="color:rgba(255,255,255,0.65)">Soporte</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5">99%</div>
                                <div class="small" style="color:rgba(255,255,255,0.65)">Puntualidad</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel de formulario --}}
                <div class="col-md-7 auth-form-panel">
                    <h4 class="fw-bold mb-1" style="color:var(--text-primary)">Iniciar Sesión</h4>
                    <p class="text-muted mb-4" style="font-size:0.9rem">Ingresa tus credenciales para continuar</p>

                    @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold small">
                                <i class="bi bi-person me-1" style="color:var(--accent)"></i>Usuario
                            </label>
                            <input type="text"
                                class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username"
                                value="{{ old('username') }}"
                                placeholder="Ingresa tu usuario"
                                required autofocus>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold small">
                                <i class="bi bi-lock me-1" style="color:var(--accent)"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password"
                                    placeholder="Ingresa tu contraseña"
                                    required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="iconPassword"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                            </button>
                        </div>
                    </form>

                    <hr style="border-color:var(--border-color)">
                    <p class="text-center text-muted small mb-0">
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="fw-semibold ms-1">Registrarse</a>
                    </p>
                    <p class="text-center mt-2 mb-0">
                        <a href="{{ route('welcome') }}" class="text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('iconPassword');
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        icon.classList.replace(isText ? 'bi-eye-slash' : 'bi-eye', isText ? 'bi-eye' : 'bi-eye-slash');
    });
</script>
@endpush

@endsection
