@extends('layouts.app')

@section('titulo', 'Gestionar Vuelos')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-airplane"></i> Gestionar Vuelos</h2>
            <a href="{{ route('operador.vuelos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Vuelo
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.vuelos.index') }}" class="row g-3">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código de vuelo...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Cancelado" {{ request('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                            <th>Código</th>
                            <th>Tipo</th>
                            <th class="text-center">Escalas</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vuelos as $vuelo)
                        <tr>
                            <td>{{ $vuelo->id }}</td>
                            <td><strong class="text-primary">{{ $vuelo->codigo_vuelo }}</strong></td>
                            <td>
                                @if($vuelo->tipo === 'Directo')
                                    <span class="badge bg-primary">Directo</span>
                                @else
                                    <span class="badge bg-warning text-dark">Con Escalas</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($vuelo->escalas_count > 0)
                                    <span class="badge bg-info">{{ $vuelo->escalas_count }} tramos</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($vuelo->estado === 'Activo')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Cancelado</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.vuelos.show', $vuelo) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('operador.vuelos.edit', $vuelo) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.vuelos.toggle-status', $vuelo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($vuelo->estado === 'Activo')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Cancelar"
                                                onclick="return confirm('¿Está seguro de cancelar el vuelo {{ $vuelo->codigo_vuelo }}?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success" title="Reactivar"
                                                onclick="return confirm('¿Está seguro de reactivar el vuelo {{ $vuelo->codigo_vuelo }}?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron vuelos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $vuelos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection