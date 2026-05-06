@extends('layouts.app')

@section('titulo', 'Gestionar Salidas')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-box-arrow-right"></i> Gestionar Salidas</h2>

        {{-- Vuelos elegibles para salida --}}
        @if($vuelosElegibles->count() > 0)
        <div class="card shadow-sm mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Vuelos Listos para Salida ({{ $vuelosElegibles->count() }})</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Aeronave</th>
                            <th>Fecha Salida</th>
                            <th class="text-center">Asientos</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vuelosElegibles as $prog)
                        <tr>
                            <td><strong class="text-primary">{{ $prog->vuelo->codigo_vuelo }}</strong></td>
                            <td>{{ $prog->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $prog->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $prog->aeronave->matricula }}</td>
                            <td>{{ $prog->fecha_salida }} {{ $prog->hora_salida }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $prog->asientos_vendidos }}/{{ $prog->aeronave->capacidad_total }}</span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('operador.salidas.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="programacion_vuelo_id" value="{{ $prog->id }}">
                                    <button type="submit" class="btn btn-success btn-sm"
                                            onclick="return confirm('¿Confirmar la salida del vuelo {{ $prog->vuelo->codigo_vuelo }}? Se generará el ingreso financiero automáticamente.')">
                                        <i class="bi bi-box-arrow-right"></i> Registrar Salida
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Historial de salidas --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Salidas</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('operador.salidas.index') }}" class="row g-3 mb-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por código de vuelo...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Vuelo</th>
                                <th>Ruta</th>
                                <th>Fecha Salida</th>
                                <th class="text-center">Monto Recaudado</th>
                                <th class="text-center">Ingreso</th>
                                <th>Registrado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salidas as $salida)
                            <tr>
                                <td>{{ $salida->id }}</td>
                                <td><strong class="text-primary">{{ $salida->programacionVuelo->vuelo->codigo_vuelo }}</strong></td>
                                <td>{{ $salida->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $salida->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                                <td>{{ $salida->programacionVuelo->fecha_salida }} {{ $salida->programacionVuelo->hora_salida }}</td>
                                <td class="text-center text-success fw-bold">${{ number_format($salida->monto_total_recaudado, 2) }}</td>
                                <td class="text-center">
                                    @if($salida->ingreso)
                                        <span class="badge bg-success"><i class="bi bi-check"></i> Generado</span>
                                    @else
                                        <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>{{ $salida->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('operador.salidas.show', $salida) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> No se encontraron salidas registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center">
                        {{ $salidas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection