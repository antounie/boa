@extends('layouts.app')

@section('titulo', 'Nueva Devolución')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-arrow-return-left"></i> Procesar Nueva Devolución</h2>
        <p class="text-muted">Busque la venta que desea cancelar y procesar la devolución.</p>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.devoluciones.create') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar_venta"
                                   value="{{ request('buscar_venta') }}"
                                   placeholder="Buscar por código de venta, nombre o documento del cliente...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar Venta</button>
                    </div>
                </form>
            </div>
        </div>

        @if($ventas->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Ventas encontradas ({{ $ventas->count() }})</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código Venta</th>
                            <th>Cliente</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Asiento</th>
                            <th>Monto</th>
                            <th>Ticket</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                        <tr>
                            <td><strong>{{ $venta->codigo_venta }}</strong></td>
                            <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}
                                <br><small class="text-muted">{{ $venta->cliente->documento_identidad }}</small></td>
                            <td>{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</td>
                            <td class="fw-bold">${{ number_format($venta->monto_total, 2) }}</td>
                            <td>
                                @if($venta->ticket)
                                    <span class="badge bg-primary">{{ $venta->ticket->numero_ticket }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.devoluciones.confirmar', $venta) }}" class="btn btn-sm btn-danger">
                                    <i class="bi bi-arrow-return-left"></i> Devolver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(request()->filled('buscar_venta'))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> No se encontraron ventas elegibles para devolución.
        </div>
        @endif

        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection