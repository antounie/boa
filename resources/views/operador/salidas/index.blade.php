@extends('layouts.app')

@section('titulo', 'Gestionar Salidas')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')

<div class="col-12">
    <h2><i class="bi bi-box-arrow-right"></i> Gestionar Salidas</h2>
</div>

{{-- Vuelos listos para salir --}}
@if($vuelosElegibles->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success d-flex align-items-center justify-content-between">
        <span class="fw-bold">
            <i class="bi bi-check-circle-fill me-2"></i>Vuelos listos para registrar salida
        </span>
        <span class="badge bg-white text-success fw-bold">{{ $vuelosElegibles->count() }}</span>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Vuelo</th>
                    <th>Ruta</th>
                    <th>Aeronave</th>
                    <th>Fecha / Hora</th>
                    <th class="text-center">Ocupación</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vuelosElegibles as $prog)
                <tr>
                    <td>
                        <strong style="color:var(--accent)">{{ $prog->vuelo->codigo_vuelo }}</strong>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="iata-badge">{{ $prog->ruta->aeropuertoOrigen->codigo_IATA }}</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="iata-badge">{{ $prog->ruta->aeropuertoDestino->codigo_IATA }}</span>
                        </div>
                    </td>
                    <td>{{ $prog->aeronave->matricula }}</td>
                    <td>
                        <div>{{ \Carbon\Carbon::parse($prog->fecha_salida)->format('d M Y') }}</div>
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($prog->hora_salida)->format('H:i') }} hrs</div>
                    </td>
                    <td class="text-center">
                        @php $pct = $prog->aeronave->capacidad_total > 0 ? round($prog->asientos_vendidos / $prog->aeronave->capacidad_total * 100) : 0; @endphp
                        <div class="fw-semibold">{{ $prog->asientos_vendidos }}/{{ $prog->aeronave->capacidad_total }}</div>
                        <div class="progress mt-1" style="height:5px;border-radius:3px">
                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                        </div>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('operador.salidas.store') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="programacion_vuelo_id" value="{{ $prog->id }}">
                            <button type="submit" class="btn btn-success btn-sm fw-semibold"
                                onclick="return confirm('¿Confirmar la salida del vuelo {{ $prog->vuelo->codigo_vuelo }}?')">
                                <i class="bi bi-send-fill me-1"></i>Registrar salida
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

{{-- Historial --}}
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-bold">
            <i class="bi bi-clock-history me-2"></i>Historial de Salidas
        </span>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ route('operador.salidas.index') }}" class="row g-2 mb-3">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por código de vuelo...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
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
                        <th class="text-end">Recaudado</th>
                        <th class="text-center">Ingreso</th>
                        <th>Registrado</th>
                        <th class="text-center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salidas as $salida)
                    <tr>
                        <td class="text-muted small">{{ $salida->id }}</td>
                        <td>
                            <strong style="color:var(--accent)">{{ $salida->programacionVuelo->vuelo->codigo_vuelo }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="iata-badge">{{ $salida->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }}</span>
                                <i class="bi bi-arrow-right text-muted small"></i>
                                <span class="iata-badge">{{ $salida->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</span>
                            </div>
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($salida->programacionVuelo->fecha_salida)->format('d M Y') }}</div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($salida->programacionVuelo->hora_salida)->format('H:i') }}</div>
                        </td>
                        <td class="text-end fw-bold" style="color:var(--accent)">
                            Bs. {{ number_format($salida->monto_total_recaudado, 2) }}
                        </td>
                        <td class="text-center">
                            @if($salida->ingreso)
                                <span class="badge bg-success"><i class="bi bi-check me-1"></i>Generado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $salida->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('operador.salidas.show', $salida) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox d-block"></i>
                            No hay salidas registradas.
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

@endsection
