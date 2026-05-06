@extends('layouts.app')

@section('titulo', 'Editar Permisos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-key"></i> Permisos del Rol: {{ $rol->nombre }}</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted">Active los módulos a los que este rol tendrá acceso completo.</p>

                <form method="POST" action="{{ route('admin.permisos.update', $rol) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="marcarTodos">
                            <label class="form-check-label fw-bold" for="marcarTodos">
                                Marcar / Desmarcar Todos
                            </label>
                        </div>
                        <hr>
                    </div>

                    <div class="row">
                        @foreach($tablas as $tabla)
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permiso-check" type="checkbox"
                                       name="acceso[]" value="{{ $tabla }}" id="tabla_{{ $tabla }}"
                                       {{ isset($permisosActuales[$tabla]) && $permisosActuales[$tabla] ? 'checked' : '' }}>
                                <label class="form-check-label" for="tabla_{{ $tabla }}">
                                    {{ $tabla }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Permisos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('marcarTodos').addEventListener('change', function() {
        const checks = document.querySelectorAll('.permiso-check');
        checks.forEach(check => check.checked = this.checked);
    });
</script>
@endpush