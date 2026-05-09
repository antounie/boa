@extends('layouts.app')

@section('titulo', 'Registrarse')

@section('contenido')
<div class="auth-wrapper py-4">
    <div class="w-100" style="max-width: 960px;">
        <div class="shadow-lg overflow-hidden" style="border-radius:var(--card-radius)">
            <div class="row g-0">

                {{-- Panel de marca --}}
                <div class="col-md-4 auth-brand-panel">
                    <div class="position-relative z-1">
                        <div class="mb-4">
                            <i class="bi bi-airplane-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.9)"></i>
                        </div>
                        <h1 class="fw-bold mb-2" style="font-size:2rem">BoA</h1>
                        <p class="mb-4" style="color:rgba(255,255,255,0.8)">Boliviana de Aviación</p>
                        <hr style="border-color:rgba(255,255,255,0.2)">
                        <p class="small mt-3" style="color:rgba(255,255,255,0.7);line-height:1.6">
                            Crea tu cuenta para reservar vuelos, consultar tickets y disfrutar de todos nuestros servicios.
                        </p>
                        <div class="mt-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-white opacity-75"></i>
                                <span class="small" style="color:rgba(255,255,255,0.8)">Reservas online 24/7</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-white opacity-75"></i>
                                <span class="small" style="color:rgba(255,255,255,0.8)">Tickets digitales</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-white opacity-75"></i>
                                <span class="small" style="color:rgba(255,255,255,0.8)">Historial de viajes</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel de formulario --}}
                <div class="col-md-8 auth-form-panel">
                    <h4 class="fw-bold mb-1" style="color:var(--text-primary)">Crear Cuenta</h4>
                    <p class="text-muted mb-4" style="font-size:0.9rem">Completa los datos para registrarte como pasajero</p>

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        {{-- Datos personales --}}
                        <div class="form-section-title">
                            <i class="bi bi-person"></i>Datos personales
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-semibold small">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Tu nombre" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label fw-semibold small">Apellido</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                    id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Tu apellido" required>
                                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="documento_identidad" class="form-label fw-semibold small">CI / Documento</label>
                                <input type="text" class="form-control @error('documento_identidad') is-invalid @enderror"
                                    id="documento_identidad" name="documento_identidad" value="{{ old('documento_identidad') }}" required>
                                @error('documento_identidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label fw-semibold small">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                    id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                                @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold small">
                                    Teléfono <span class="text-muted fw-normal">(opcional)</span>
                                </label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="+591 ...">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Datos de acceso --}}
                        <div class="form-section-title">
                            <i class="bi bi-shield-lock"></i>Datos de acceso
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="username" class="form-label fw-semibold small">Nombre de Usuario</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" placeholder="Elige un nombre único" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold small">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Mín. 8 caracteres" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="iconPassword"></i>
                                    </button>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Una mayúscula, minúscula, número y carácter especial.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold small">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control"
                                        id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="bi bi-eye" id="iconPasswordConfirm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-center">
                            <button type="submit" class="btn btn-primary fw-semibold px-4 py-2">
                                <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                            </button>
                            <a href="{{ route('login') }}" class="text-muted small">
                                ¿Ya tienes cuenta? <span class="fw-semibold" style="color:var(--link-color)">Iniciar sesión</span>
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <p class="text-center mt-3 mb-0">
            <a href="{{ route('welcome') }}" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        icon.classList.replace(isText ? 'bi-eye-slash' : 'bi-eye', isText ? 'bi-eye' : 'bi-eye-slash');
    }
    document.getElementById('togglePassword').addEventListener('click', () => togglePwd('password', 'iconPassword'));
    document.getElementById('togglePasswordConfirm').addEventListener('click', () => togglePwd('password_confirmation', 'iconPasswordConfirm'));
</script>
@endpush

@endsection
