@extends('layouts.app')

@section('titulo', 'Reporte de Vuelos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-airplane"></i> Reporte de Vuelos</h2>
            <div>
                <a href="{{ route('admin.reportes.vuelos.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Exportar PDF
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarCorreo">
                    <i class="bi bi-envelope"></i> Enviar por Correo
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.vuelos') }}" class="row g-3">
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
                            <option value="Programado" {{ request('estado') == 'Programado' ? 'selected' : '' }}>Programado</option>
                            <option value="Completo" {{ request('estado') == 'Completo' ? 'selected' : '' }}>Completo</option>
                            <option value="Salido" {{ request('estado') == 'Salido' ? 'selected' : '' }}>Salido</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="{{ route('admin.reportes.vuelos') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white text-center"><div class="card-body"><h6>Programados</h6><h3>{{ $totales['programados'] }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark text-center"><div class="card-body"><h6>Completos</h6><h3>{{ $totales['completos'] }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white text-center"><div class="card-body"><h6>Salidos</h6><h3>{{ $totales['salidos'] }}</h3></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white text-center"><div class="card-body"><h6>Total</h6><h3>{{ $totales['total'] }}</h3></div></div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Aeronave</th>
                            <th>Fecha Salida</th>
                            <th>Hora</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Vendidos</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programaciones as $prog)
                        <tr>
                            <td><strong class="text-primary">{{ $prog->vuelo->codigo_vuelo }}</strong></td>
                            <td>{{ $prog->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $prog->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $prog->aeronave->matricula }}</td>
                            <td>{{ $prog->fecha_salida }}</td>
                            <td>{{ $prog->hora_salida }}</td>
                            <td class="text-center">${{ number_format($prog->precio_base, 2) }}</td>
                            <td class="text-center">{{ $prog->asientos_vendidos }}/{{ $prog->aeronave->capacidad_total }}</td>
                            <td>
                                <span class="badge bg-{{ $prog->estado === 'Programado' ? 'primary' : ($prog->estado === 'Completo' ? 'warning' : 'success') }}">
                                    {{ $prog->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron resultados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>
{{-- Modal Enviar por Correo --}}
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.reportes.enviar-correo') }}">
                @csrf
                <input type="hidden" name="tipo" value="vuelos">
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
                        <small class="text-muted">Se enviará el reporte de vuelos en formato PDF.</small>
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