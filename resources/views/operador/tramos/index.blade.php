@extends('layouts.app')

@section('titulo', 'Tramos de Vuelo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-diagram-3"></i> Tramos de Vuelo</h2>
            <a href="{{ route('operador.tramos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Tramo
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.tramos.index') }}" class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por ciudad o código IATA...">
                        </div>
                    </div>
                    <div class="col-md-4">
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
                            <th>Destino</th>
                            <th>Duración</th>
                            <th>Tipo</th>
                            <th>Sub-tramos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tramos as $tramo)
                        <tr>
                            <td>{{ $tramo->id }}</td>
                            <td>
                                <strong>{{ $tramo->aeropuertoOrigen->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $tramo->aeropuertoOrigen->ciudad }}</small>
                            </td>
                            <td>
                                <strong>{{ $tramo->aeropuertoDestino->codigo_IATA }}</strong>
                                <br><small class="text-muted">{{ $tramo->aeropuertoDestino->ciudad }}</small>
                            </td>
                            <td>{{ $tramo->duracion_estimada }}</td>
                            <td>
                                @if($tramo->subTramos->count() > 0)
                                    <span class="badge bg-warning text-dark">Con Escalas</span>
                                @else
                                    <span class="badge bg-success">Directo</span>
                                @endif
                            </td>
                            <td>
                                @if($tramo->subTramos->count() > 0)
                                    <span class="badge bg-secondary">{{ $tramo->subTramos->count() }} sub-tramo(s)</span>
                                    <div class="mt-1">
                                        @foreach($tramo->subTramos as $sub)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-arrow-return-right"></i>
                                            {{ $sub->aeropuertoOrigen->codigo_IATA }} → {{ $sub->aeropuertoDestino->codigo_IATA }}
                                            ({{ $sub->duracion_estimada }})
                                        </small>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.tramos.show', $tramo) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('operador.tramos.edit', $tramo) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.tramos.destroy', $tramo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Eliminar este tramo?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron tramos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $tramos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
