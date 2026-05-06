@extends('layouts.app')

@section('titulo', 'Gestionar Aeropuertos')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-geo-alt"></i> Gestionar Aeropuertos</h2>
            <a href="{{ route('operador.aeropuertos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Aeropuerto
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.aeropuertos.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código IATA, nombre, ciudad o país...">
                        </div>
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
                            <th>Código IATA</th>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>País</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aeropuertos as $aeropuerto)
                        <tr>
                            <td>{{ $aeropuerto->id }}</td>
                            <td><strong class="text-primary">{{ $aeropuerto->codigo_IATA }}</strong></td>
                            <td>{{ $aeropuerto->nombre }}</td>
                            <td>{{ $aeropuerto->ciudad }}</td>
                            <td>{{ $aeropuerto->pais }}</td>
                            <td class="text-center">
                                <a href="{{ route('operador.aeropuertos.edit', $aeropuerto) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.aeropuertos.destroy', $aeropuerto) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar el aeropuerto {{ $aeropuerto->codigo_IATA }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron aeropuertos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $aeropuertos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection