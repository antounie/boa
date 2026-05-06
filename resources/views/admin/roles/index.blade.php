@extends('layouts.app')

@section('titulo', 'Gestionar Roles')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-shield-lock"></i> Gestionar Roles</h2>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Rol
            </a>
        </div>

        {{-- Buscador --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por nombre o descripción...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de roles --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Usuarios</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $rol)
                        <tr>
                            <td>{{ $rol->id }}</td>
                            <td><strong>{{ $rol->nombre }}</strong></td>
                            <td>{{ $rol->descripcion ?? 'Sin descripción' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $rol->usuarios_count }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.roles.edit', $rol) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!in_array($rol->nombre, ['Administrador', 'Operador', 'Cliente']))
                                <form action="{{ route('admin.roles.destroy', $rol) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar el rol {{ $rol->nombre }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron roles.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $roles->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection