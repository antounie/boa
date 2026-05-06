@extends('layouts.app')

@section('titulo', 'Reporte de Ingresos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-graph-up-arrow"></i> Reporte de Ingresos</h2>
            <div>
                <a href="{{ route('admin.reportes.ingresos.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarCorreo">
                    <i class="bi bi-envelope"></i> Enviar por Correo
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.ingresos') }}" class="row g-3">
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
                        <a href="{{ route('admin.reportes.ingresos') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white text-center"><div class="card-body"><h6>Monto Total</h6><h3>${{ number_format($totales['monto_total'], 2) }}</h3></div></div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white text-center"><div class="card-body"><h6>Total Pasajes</h6><h3>{{ $totales['pasajes_total'] }}</h3></div></div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white text-center"><div class="card-body"><h6>Registros</h6><h3>{{ $totales['cantidad'] }}</h3></div></div>
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
                            <th class="text-center">Monto</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                        <tr>
                            <td>{{ $ingreso->id }}</td>
                            <td><strong class="text-primary">{{ $ingreso->programacionVuelo->vuelo->codigo_vuelo }}</strong></td>
                            <td>{{ $ingreso->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ingreso->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td class="text-center">{{ $ingreso->cantidad_pasajes }}</td>
                            <td class="text-center text-success fw-bold">${{ number_format($ingreso->monto_total, 2) }}</td>
                            <td>{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No se encontraron resultados.</td></tr>
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
                <input type="hidden" name="tipo" value="ingresos">
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
                        <small class="text-muted">Se enviará el reporte de ingresos en formato PDF.</small>
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