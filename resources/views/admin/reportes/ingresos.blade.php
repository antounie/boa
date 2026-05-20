@extends('layouts.app')

@section('titulo', 'Reporte de Ingresos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="bi bi-graph-up-arrow me-2" style="color:var(--accent)"></i>Reporte de Ingresos</h2>
    <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Reportes
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Registros</div>
                <div class="kpi-value">{{ $totales['cantidad'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Pasajes totales</div>
                <div class="kpi-value">{{ $totales['pasajes_total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Monto total</div>
                <div class="kpi-value" style="font-size:1.4rem">Bs. {{ number_format($totales['monto_total'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Promedio por vuelo</div>
                <div class="kpi-value" style="font-size:1.4rem">Bs. {{ number_format($totales['promedio'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">
        <i class="bi bi-funnel me-2"></i>Filtros
    </div>
    <div class="card-body">
        <div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
            <span class="text-muted small">Rango rápido:</span>
            <button type="button" class="btn btn-sm btn-outline-secondary rango-btn" onclick="setRango('hoy')">Hoy</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rango-btn" onclick="setRango('semana')">Esta semana</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rango-btn" onclick="setRango('mes')">Este mes</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rango-btn" onclick="setRango('anio')">Este año</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rango-btn" onclick="setRango('todo')">Todo</button>
        </div>

        <form id="formFiltros" method="GET" action="{{ route('admin.reportes.ingresos') }}" class="row g-2">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm"
                    value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm"
                    value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Origen</label>
                <select name="aeropuerto_origen" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($aeropuertos as $ap)
                        <option value="{{ $ap->id }}" {{ request('aeropuerto_origen') == $ap->id ? 'selected' : '' }}>
                            {{ $ap->codigo_IATA }} – {{ $ap->ciudad }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Destino</label>
                <select name="aeropuerto_destino" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($aeropuertos as $ap)
                        <option value="{{ $ap->id }}" {{ request('aeropuerto_destino') == $ap->id ? 'selected' : '' }}>
                            {{ $ap->codigo_IATA }} – {{ $ap->ciudad }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="{{ route('admin.reportes.ingresos') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.reportes.ingresos.pdf', request()->query()) }}"
                   class="btn btn-danger btn-sm">
                    <i class="bi bi-file-pdf me-1"></i>PDF
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalCorreo">
                    <i class="bi bi-envelope me-1"></i>Enviar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Gráfico --}}
@if($ingresos->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold"><i class="bi bi-graph-up me-2"></i>Ingresos por mes (Bs.)</div>
    <div class="card-body">
        <canvas id="chartIngresos" height="80"></canvas>
    </div>
</div>
@endif

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table me-2"></i>Detalle</span>
        <span class="badge" style="background:var(--btn-primary-bg)">{{ $ingresos->count() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Vuelo</th>
                        <th>Ruta</th>
                        <th class="text-center">Pasajes</th>
                        <th class="text-end">Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingresos as $ingreso)
                    <tr>
                        <td class="text-muted small">{{ $ingreso->id }}</td>
                        <td><strong style="color:var(--accent)">{{ $ingreso->programacionVuelo->codigo_vuelo }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="iata-badge">{{ $ingreso->programacionVuelo->aeropuertoOrigen->codigo_IATA }}</span>
                                <i class="bi bi-arrow-right text-muted small"></i>
                                <span class="iata-badge">{{ $ingreso->programacionVuelo->aeropuertoDestino->codigo_IATA }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ $ingreso->cantidad_pasajes }}</td>
                        <td class="text-end fw-bold" style="color:var(--accent)">
                            Bs. {{ number_format($ingreso->monto_total, 2) }}
                        </td>
                        <td class="text-muted small">{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem"></i>
                            No hay ingresos con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal correo --}}
<div class="modal fade" id="modalCorreo" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('admin.reportes.enviar-correo') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="ingresos">
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                <input type="hidden" name="aeropuerto_origen" value="{{ request('aeropuerto_origen') }}">
                <input type="hidden" name="aeropuerto_destino" value="{{ request('aeropuerto_destino') }}">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-envelope me-2"></i>Enviar reporte</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-semibold">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required placeholder="correo@ejemplo.com">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send me-1"></i>Enviar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function setRango(r) {
    const hoy = new Date();
    let ini, fin = hoy.toISOString().split('T')[0];
    if (r === 'hoy') {
        ini = fin;
    } else if (r === 'semana') {
        const l = new Date(hoy);
        l.setDate(hoy.getDate() - hoy.getDay() + 1);
        ini = l.toISOString().split('T')[0];
    } else if (r === 'mes') {
        ini = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
    } else if (r === 'anio') {
        ini = new Date(hoy.getFullYear(), 0, 1).toISOString().split('T')[0];
    } else {
        document.getElementById('fecha_inicio').value = '';
        document.getElementById('fecha_fin').value = '';
        document.getElementById('formFiltros').submit();
        return;
    }
    document.getElementById('fecha_inicio').value = ini;
    document.getElementById('fecha_fin').value = fin;
    document.getElementById('formFiltros').submit();
}

@if($ingresos->count() > 0)
(function() {
    const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#198754';
    new Chart(document.getElementById('chartIngresos').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! $chartLabels !!},
            datasets: [{
                label: 'Ingresos (Bs.)',
                data: {!! $chartData !!},
                fill: true,
                backgroundColor: accent + '22',
                borderColor: accent,
                borderWidth: 2,
                pointBackgroundColor: accent,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
})();
@endif
</script>
@endpush
