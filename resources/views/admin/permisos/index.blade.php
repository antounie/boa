@extends('layouts.app')

@section('titulo', 'Gestionar Permisos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-key-fill" style="color:var(--accent)"></i> Gestionar Permisos</h2>
    <span class="text-muted small">Configura el acceso por módulo para cada rol</span>
</div>

<div class="row g-4">
    @foreach($roles as $rol)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    <i class="bi bi-shield-lock me-1"></i>{{ $rol->nombre }}
                </span>
                <a href="{{ route('admin.permisos.edit', $rol) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($tablas as $tabla)
                    @php $tiene = isset($rol->permisos_data[$tabla]) && $rol->permisos_data[$tabla]; @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2"
                        style="background:var(--card-bg);border-color:var(--border-color)">
                        <span class="small" style="color:var(--text-primary)">
                            {{ $tabla }}
                        </span>
                        @if($tiene)
                            <span class="badge" style="background:var(--btn-primary-bg);font-size:0.7rem">
                                <i class="bi bi-check-lg me-1"></i>Activo
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-25 text-secondary" style="font-size:0.7rem">
                                Sin acceso
                            </span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
