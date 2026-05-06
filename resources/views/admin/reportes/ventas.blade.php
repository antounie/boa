@extends('layouts.app')

@section('titulo', 'Reporte de Ventas')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cart-check"></i> Reporte de Ventas</h2>
            <div>
                <a href="{{ route('admin.reportes.ventas.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarCorreo">
                    <i class="bi bi-envelope"></i> Enviar por Correo
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.ventas') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="Confirmada" {{ request('estado') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white text-center"><div class="card-body"><h6>Confirmadas</h6><h3>{{ $totales['confirmadas'] }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white text-center"><div class="card-body"><h6>Canceladas</h6><h3>{{ $totales['canceladas'] }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white text-center"><div class="card-body"><h6>Monto Confirmadas</h6><h3>${{ number_format($totales['monto_confirmadas'], 2) }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white text-center"><div class="card-body"><h6>Total Ventas</h6><h3>{{ $totales['total'] }}</h3></div></div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Asiento</th>
                            <th>Pago</th>
                            <th class="text-center">Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td><strong>{{ $venta->codigo_venta }}</strong></td>
                            <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</td>
                            <td>{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</td>
                            <td>{{ $venta->metodo_pago }}</td>
                            <td class="text-center fw-bold">${{ number_format($venta->monto_total, 2) }}</td>
                            <td><span class="badge bg-{{ $venta->estado === 'Confirmada' ? 'success' : 'danger' }}">{{ $venta->estado }}</span></td>
                            <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron resultados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.reportes.enviar-correo') }}">
                @csrf
                <input type="hidden" name="tipo" value="ventas">
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-envelope"></i> Enviar Reporte por Correo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico del destinatario</label>
                        <input type="email" class="form-control" name="email" required placeholder="ejemplo@boa.go.bo">
                        <small class="text-muted">Se enviará el reporte de ventas en formato PDF.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection