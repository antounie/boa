@extends('layouts.app')

@section('titulo', 'Reporte de Egresos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-graph-down-arrow"></i> Reporte de Egresos</h2>
            <div>
                <a href="{{ route('admin.reportes.egresos.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarCorreo">
                    <i class="bi bi-envelope"></i> Enviar por Correo
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.egresos') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="{{ route('admin.reportes.egresos') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-danger text-white text-center"><div class="card-body"><h6>Monto Total Egresos</h6><h3>${{ number_format($totales['monto_total'], 2) }}</h3></div></div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark text-white text-center"><div class="card-body"><h6>Total Devoluciones</h6><h3>{{ $totales['cantidad'] }}</h3></div></div>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($egresos as $egreso)
                        <tr>
                            <td>{{ $egreso->id }}</td>
                            <td><strong>{{ $egreso->devolucion->venta->codigo_venta }}</strong></td>
                            <td>{{ $egreso->devolucion->cliente->nombre }} {{ $egreso->devolucion->cliente->apellido }}</td>
                            <td>{{ $egreso->devolucion->venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ Str::limit($egreso->devolucion->motivo, 40) }}</td>
                            <td class="text-center text-danger fw-bold">${{ number_format($egreso->monto_devuelto, 2) }}</td>
                            <td>{{ $egreso->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No se encontraron resultados.</td></tr>
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
                <input type="hidden" name="tipo" value="egresos">
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-envelope"></i> Enviar Reporte por Correo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico del destinatario</label>
                        <input type="email" class="form-control" name="email" required placeholder="ejemplo@boa.go.bo">
                        <small class="text-muted">Se enviará el reporte de egresos en formato PDF.</small>
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