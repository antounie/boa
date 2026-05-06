@extends('layouts.app')

@section('titulo', 'Gestionar Egresos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-graph-down-arrow"></i> Gestionar Egresos</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-danger text-white text-center">
                    <div class="card-body">
                        <h6>Total Egresos</h6>
                        <h3>${{ number_format($totalEgresos, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-dark text-center">
                    <div class="card-body">
                        <h6>Total Devoluciones</h6>
                        <h3>{{ $totalDevoluciones }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.egresos.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por cliente o código de venta...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="fecha_inicio"
                               value="{{ request('fecha_inicio') }}" placeholder="Fecha inicio">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="fecha_fin"
                               value="{{ request('fecha_fin') }}" placeholder="Fecha fin">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
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
                            <th>Motivo</th>
                            <th class="text-center">Monto Devuelto</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($egresos as $egreso)
                        <tr>
                            <td>{{ $egreso->id }}</td>
                            <td><strong>{{ $egreso->devolucion->venta->codigo_venta }}</strong></td>
                            <td>{{ $egreso->devolucion->cliente->nombre }} {{ $egreso->devolucion->cliente->apellido }}</td>
                            <td>{{ $egreso->devolucion->venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ Str::limit($egreso->devolucion->motivo, 30) }}</td>
                            <td class="text-center text-danger fw-bold">${{ number_format($egreso->monto_devuelto, 2) }}</td>
                            <td>{{ $egreso->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.egresos.show', $egreso) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron egresos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $egresos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection