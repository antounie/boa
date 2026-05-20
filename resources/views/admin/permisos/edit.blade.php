@extends('layouts.app')

@section('titulo', 'Editar Permisos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')

@php
$categorias = [
    ['icono'=>'bi-people-fill',       'label'=>'Administración',        'tablas'=>['usuarios','roles','permisos','clientes']],
    ['icono'=>'bi-airplane',          'label'=>'Programación de Vuelos','tablas'=>['aeropuertos','tipo_clases','aeronaves','asientos','rutas','tramos','programacion_vuelos']],
    ['icono'=>'bi-person-badge-fill', 'label'=>'Personal',              'tablas'=>['empleados','tripulaciones']],
    ['icono'=>'bi-shop',              'label'=>'Comercial',             'tablas'=>['ventas','devoluciones']],
    ['icono'=>'bi-box-arrow-right',   'label'=>'Operaciones',           'tablas'=>['salidas']],
    ['icono'=>'bi-cash-stack',        'label'=>'Financiero',            'tablas'=>['ingresos','egresos']],
    ['icono'=>'bi-bar-chart-fill',    'label'=>'Reportes',              'tablas'=>['reportes']],
    ['icono'=>'bi-ticket-perforated', 'label'=>'Acceso Cliente',        'tablas'=>['reservas','compras','tickets']],
];
@endphp

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.permisos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0 fw-bold" style="font-size:1.35rem;color:var(--text-primary)">
                <i class="bi bi-key-fill me-2" style="color:var(--accent)"></i>
                Permisos: <span style="color:var(--accent)">{{ $rol->nombre }}</span>
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.permisos.update', $rol) }}">
            @csrf
            @method('PUT')

            {{-- Seleccionar todos --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <span class="fw-semibold" style="color:var(--text-primary)">Seleccionar / deseleccionar todos los módulos</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="marcarTodos"
                               style="width:2.5em;height:1.3em;cursor:pointer">
                    </div>
                </div>
            </div>

            {{-- Categorías --}}
            @foreach($categorias as $cat)
            @php
                $tablasEnCategoria = array_filter($cat['tablas'], fn($t) => in_array($t, $tablas));
            @endphp
            @if(count($tablasEnCategoria) > 0)
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2">
                    <span class="fw-semibold small">
                        <i class="bi {{ $cat['icono'] }} me-2"></i>{{ $cat['label'] }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        @foreach($tablasEnCategoria as $tabla)
                        <div class="col-sm-6 col-md-4">
                            <div class="permiso-switch-card">
                                <label for="tabla_{{ $tabla }}" class="form-check-label">
                                    {{ str_replace('_', ' ', ucfirst($tabla)) }}
                                </label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input permiso-check" type="checkbox" role="switch"
                                           name="acceso[]" value="{{ $tabla }}" id="tabla_{{ $tabla }}"
                                           {{ isset($permisosActuales[$tabla]) && $permisosActuales[$tabla] ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endforeach

            {{-- Acciones --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary fw-semibold px-4">
                    <i class="bi bi-check-lg me-1"></i>Guardar Permisos
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    const marcarTodos = document.getElementById('marcarTodos');
    const checks = document.querySelectorAll('.permiso-check');

    // Estado inicial del "todos"
    marcarTodos.checked = [...checks].every(c => c.checked);
    marcarTodos.indeterminate = !marcarTodos.checked && [...checks].some(c => c.checked);

    marcarTodos.addEventListener('change', () => {
        checks.forEach(c => c.checked = marcarTodos.checked);
    });

    checks.forEach(c => c.addEventListener('change', () => {
        const total = checks.length, checked = [...checks].filter(c => c.checked).length;
        marcarTodos.checked = checked === total;
        marcarTodos.indeterminate = checked > 0 && checked < total;
    }));
</script>
@endpush

@endsection
