@extends('layouts.app')

@section('titulo', 'Gestionar Empleados')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-badge"></i> Gestionar Empleados</h2>
            <a href="{{ route('operador.empleados.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Empleado
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.empleados.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por nombre, apellido o licencia...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="cargo">
                            <option value="">Todos los cargos</option>
                            <option value="Piloto" {{ request('cargo') == 'Piloto' ? 'selected' : '' }}>Piloto</option>
                            <option value="Copiloto" {{ request('cargo') == 'Copiloto' ? 'selected' : '' }}>Copiloto</option>
                            <option value="Auxiliar" {{ request('cargo') == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre Completo</th>
                            <th>Cargo</th>
                            <th>Licencia</th>
                            <th class="text-center">Vuelos Asignados</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $empleado)
                        <tr>
                            <td>{{ $empleado->id }}</td>
                            <td><strong>{{ $empleado->nombre }} {{ $empleado->apellido }}</strong></td>
                            <td>
                                @if($empleado->cargo === 'Piloto')
                                    <span class="badge bg-primary">Piloto</span>
                                @elseif($empleado->cargo === 'Copiloto')
                                    <span class="badge bg-info">Copiloto</span>
                                @else
                                    <span class="badge bg-secondary">Auxiliar</span>
                                @endif
                            </td>
                            <td>{{ $empleado->licencia ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $empleado->tripulaciones_count }}</span>
                            </td>
                            <td>
                                @if($empleado->estado === 'Activo')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.empleados.edit', $empleado) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.empleados.toggle-status', $empleado) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($empleado->estado === 'Activo')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Desactivar"
                                                onclick="return confirm('¿Está seguro de desactivar a {{ $empleado->nombre }} {{ $empleado->apellido }}?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success" title="Reactivar"
                                                onclick="return confirm('¿Está seguro de reactivar a {{ $empleado->nombre }} {{ $empleado->apellido }}?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron empleados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $empleados->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection