@extends('layouts.app')

@section('titulo', 'Gestionar Devoluciones')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-arrow-return-left"></i> Gestionar Devoluciones</h2>
            <a href="{{ route('admin.devoluciones.create') }}" class="btn btn-danger">
                <i class="bi bi-plus-lg"></i> Nueva Devolución
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.devoluciones.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código de venta, nombre o documento del cliente...">
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
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th class="text-center">Monto Devuelto</th>
                            <th>Motivo</th>
                            <th>Egreso</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devoluciones as $devolucion)
                        <tr>
                            <td>{{ $devolucion->id }}</td>
                            <td><strong>{{ $devolucion->venta->codigo_venta }}</strong></td>
                            <td>{{ $devolucion->cliente->nombre }} {{ $devolucion->cliente->apellido }}</td>
                            <td>{{ $devolucion->venta->programacionVuelo->codigo_vuelo }}</td>
                            <td>{{ $devolucion->venta->programacionVuelo->aeropuertoOrigen->codigo_IATA }} → {{ $devolucion->venta->programacionVuelo->aeropuertoDestino->codigo_IATA }}</td>
                            <td class="text-center text-danger fw-bold">${{ number_format($devolucion->monto_devolucion, 2) }}</td>
                            <td>{{ Str::limit($devolucion->motivo, 30) }}</td>
                            <td>
                                @if($devolucion->egreso)
                                    <span class="badge bg-success"><i class="bi bi-check"></i> Generado</span>
                                @else
                                    <span class="badge bg-warning">Pendiente</span>
                                @endif
                            </td>
                            <td>{{ $devolucion->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.devoluciones.show', $devolucion) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron devoluciones.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $devoluciones->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection