@extends('layouts.app')

@section('titulo', 'Gestionar Usuarios')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Gestionar Usuarios</h2>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
        </div>

        {{-- Buscador --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por nombre, apellido, username o email...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de usuarios --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td><strong>{{ $usuario->username }}</strong></td>
                            <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge bg-info">{{ $usuario->rol->nombre }}</span>
                            </td>
                            <td>
                                @if($usuario->estado === 'Activo')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Bloqueado
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.usuarios.toggle-status', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($usuario->estado === 'Activo')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Bloquear"
                                                onclick="return confirm('¿Está seguro de bloquear a {{ $usuario->username }}?')">
                                            <i class="bi bi-lock"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success" title="Desbloquear"
                                                onclick="return confirm('¿Está seguro de desbloquear a {{ $usuario->username }}?')">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron usuarios.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginación --}}
                <div class="d-flex justify-content-center">
                    {{ $usuarios->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection