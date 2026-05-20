@extends('layouts.app')

@section('titulo', 'Gestionar Rutas')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-signpost-2"></i> Gestionar Rutas</h2>
            <a href="{{ route('operador.rutas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Ruta
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.rutas.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por ciudad o código IATA de origen/destino...">
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
                            <th>Origen</th>
                            <th></th>
                            <th>Destino</th>
                            <th>Distancia (km)</th>
                            <th>Duración</th>
                            <th>Tipo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutas as $ruta)
                        <tr>
                            <td>{{ $ruta->id }}</td>
                            <td>
                                <strong class="text-primary">{{ $ruta->aeropuertoOrigen->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $ruta->aeropuertoOrigen->ciudad }}</small>
                            </td>
                            <td class="text-center"><i class="bi bi-arrow-right text-primary"></i></td>
                            <td>
                                <strong class="text-primary">{{ $ruta->aeropuertoDestino->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $ruta->aeropuertoDestino->ciudad }}</small>
                            </td>
                            <td>{{ number_format($ruta->distancia, 0) }} km</td>
                            <td>{{ $ruta->duracion_estimada }}</td>
                            <td>
                                @if($ruta->tipo === 'Nacional')
                                    <span class="badge bg-success">Nacional</span>
                                @else
                                    <span class="badge bg-info">Internacional</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.rutas.show', $ruta) }}" class="btn btn-sm btn-info" title="Ver tramos">
                                    <i class="bi bi-diagram-3"></i>
                                </a>
                                <a href="{{ route('operador.rutas.edit', $ruta) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.rutas.destroy', $ruta) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar esta ruta?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron rutas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $rutas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection