@extends('layouts.app')

@section('titulo', 'Programación de Vuelos')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar3"></i> Programación de Vuelos</h2>
            <a href="{{ route('operador.programaciones.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Programación
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.programaciones.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código de vuelo, ciudad o IATA...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="Programado" {{ request('estado') == 'Programado' ? 'selected' : '' }}>Programado</option>
                            <option value="Completo" {{ request('estado') == 'Completo' ? 'selected' : '' }}>Completo</option>
                            <option value="Salido" {{ request('estado') == 'Salido' ? 'selected' : '' }}>Salido</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Aeronave</th>
                            <th>Salida</th>
                            <th>Llegada</th>
                            <th class="text-center">Precio</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programaciones as $prog)
                        <tr>
                            <td>{{ $prog->id }}</td>
                            <td>
                                <strong class="text-primary">{{ $prog->codigo_vuelo }}</strong>
                            </td>
                            <td>
                                {{ $prog->aeropuertoOrigen->codigo_IATA }}
                                <i class="bi bi-arrow-right text-primary"></i>
                                {{ $prog->aeropuertoDestino->codigo_IATA }}
                            </td>
                            <td>{{ $prog->aeronave->matricula }}</td>
                            <td>
                                {{ $prog->fecha_salida }}<br>
                                <small class="text-muted">{{ $prog->hora_salida }}</small>
                            </td>
                            <td>
                                {{ $prog->fecha_llegada }}<br>
                                <small class="text-muted">{{ $prog->hora_llegada }}</small>
                            </td>
                            <td class="text-center">${{ number_format($prog->precio_base, 2) }}</td>
                            <td>
                                @if($prog->estado === 'Programado')
                                    <span class="badge bg-primary">Programado</span>
                                @elseif($prog->estado === 'Completo')
                                    <span class="badge bg-warning text-dark">Completo</span>
                                @else
                                    <span class="badge bg-success">Salido</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.programaciones.show', $prog) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($prog->estado !== 'Salido')
                                <a href="{{ route('operador.programaciones.reprogramar', $prog) }}" class="btn btn-sm btn-secondary" title="Reprogramar">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                                @endif
                                @if($prog->estado === 'Programado')
                                <a href="{{ route('operador.programaciones.edit', $prog) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.programaciones.destroy', $prog) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar esta programación?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron programaciones.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $programaciones->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection