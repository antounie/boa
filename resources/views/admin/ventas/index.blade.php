@extends('layouts.app')

@section('titulo', 'Gestionar Ventas')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-cart-check"></i> Gestionar Ventas</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h6>Total Recaudado</h6>
                        <h3>${{ number_format($totalVentas, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <h6>Ventas Confirmadas</h6>
                        <h3>{{ $cantidadVentas }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white text-center">
                    <div class="card-body">
                        <h6>Ventas Canceladas</h6>
                        <h3>{{ $cantidadCanceladas }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.ventas.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Código, cliente o vuelo...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="Confirmada" {{ request('estado') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="fecha_inicio"
                               value="{{ request('fecha_inicio') }}" placeholder="Desde">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="fecha_fin"
                               value="{{ request('fecha_fin') }}" placeholder="Hasta">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary me-2">Filtrar</button>
                        <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                            <th>Cliente</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Asiento</th>
                            <th>Pago</th>
                            <th class="text-center">Monto</th>
                            <th>Ticket</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->id }}</td>
                            <td><strong>{{ $venta->codigo_venta }}</strong></td>
                            <td>
                                {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}
                                <br><small class="text-muted">{{ $venta->cliente->documento_identidad }}</small>
                            </td>
                            <td><strong class="text-primary">{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</strong></td>
                            <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $venta->asiento->numero }} <small class="text-muted">({{ $venta->asiento->tipoClase->nombre }})</small></td>
                            <td><span class="badge bg-info">{{ $venta->metodo_pago }}</span></td>
                            <td class="text-center fw-bold">${{ number_format($venta->monto_total, 2) }}</td>
                            <td>
                                @if($venta->ticket)
                                    <span class="badge bg-{{ $venta->ticket->estado === 'Emitido' ? 'primary' : 'danger' }}">
                                        {{ $venta->ticket->numero_ticket }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($venta->estado === 'Confirmada')
                                    <span class="badge bg-success">Confirmada</span>
                                @else
                                    <span class="badge bg-danger">Cancelada</span>
                                @endif
                            </td>
                            <td><small>{{ $venta->created_at->format('d/m/Y H:i') }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('admin.ventas.show', $venta) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron ventas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $ventas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection