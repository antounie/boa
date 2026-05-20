@extends('layouts.app')

@section('titulo', 'Gestionar Ingresos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-graph-up-arrow"></i> Gestionar Ingresos</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h6>Total Ingresos</h6>
                        <h3>${{ number_format($totalIngresos, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <h6>Total Pasajes Vendidos</h6>
                        <h3>{{ $totalPasajes }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.ingresos.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código de vuelo...">
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
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th class="text-center">Pasajes</th>
                            <th class="text-center">Monto Total</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                        <tr>
                            <td>{{ $ingreso->id }}</td>
                            <td><strong class="text-primary">{{ $ingreso->programacionVuelo->codigo_vuelo }}</strong></td>
                            <td>{{ $ingreso->programacionVuelo->aeropuertoOrigen->codigo_IATA }} → {{ $ingreso->programacionVuelo->aeropuertoDestino->codigo_IATA }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $ingreso->cantidad_pasajes }}</span></td>
                            <td class="text-center text-success fw-bold">${{ number_format($ingreso->monto_total, 2) }}</td>
                            <td>{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.ingresos.show', $ingreso) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron ingresos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $ingresos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection