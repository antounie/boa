@extends('layouts.app')

@section('titulo', 'Reporte de Ventas')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="bi bi-cart-check me-2" style="color:var(--accent)"></i>Reporte de Ventas</h2>
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
                <div class="text-muted small mb-1">Total ventas</div>
                <div class="kpi-value">{{ $totales['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Confirmadas</div>
                <div class="kpi-value" style="color:#198754">{{ $totales['confirmadas'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card kpi-danger h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Canceladas</div>
                <div class="kpi-value" style="color:#dc3545">{{ $totales['canceladas'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Monto confirmadas</div>
                <div class="kpi-value" style="font-size:1.4rem">Bs. {{ number_format($totales['monto_confirmadas'], 2) }}</div>
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

        <form id="formFiltros" method="GET" action="{{ route('admin.reportes.ventas') }}" class="row g-2">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm"
                    value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm"
                    value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="Confirmada" {{ request('estado') === 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="Cancelada"  {{ request('estado') === 'Cancelada'  ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Método de pago</label>
                <select name="metodo_pago" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($metodosPago as $mp)
                        <option value="{{ $mp }}" {{ request('metodo_pago') === $mp ? 'selected' : '' }}>{{ $mp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Clase</label>
                <select name="clase_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($tipoClases as $tc)
                        <option value="{{ $tc->id }}" {{ request('clase_id') == $tc->id ? 'selected' : '' }}>
                            {{ $tc->nombre }}
                        </option>
                    @endforeach
                </select>
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
            <div class="col-md-10 d-flex gap-2 align-items-end flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('admin.reportes.ventas.pdf', request()->query()) }}"
                       class="btn btn-danger btn-sm">
                        <i class="bi bi-file-pdf me-1"></i>PDF
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalCorreo">
                        <i class="bi bi-envelope me-1"></i>Enviar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Gráfico --}}
@if($ventas->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold"><i class="bi bi-bar-chart me-2"></i>Monto por mes (Bs.)</div>
    <div class="card-body">
        <canvas id="chartVentas" height="80"></canvas>
    </div>
</div>
@endif

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table me-2"></i>Detalle</span>
        <span class="badge" style="background:var(--btn-primary-bg)">{{ $ventas->count() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Vuelo</th>
                        <th>Ruta</th>
                        <th>Clase</th>
                        <th>Pago</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                    <tr>
                        <td><strong style="color:var(--accent)">{{ $v->codigo_venta }}</strong></td>
                        <td>{{ $v->cliente->nombre }} {{ $v->cliente->apellido }}</td>
                        <td>{{ $v->programacionVuelo->vuelo->codigo_vuelo }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="iata-badge">{{ $v->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }}</span>
                                <i class="bi bi-arrow-right text-muted small"></i>
                                <span class="iata-badge">{{ $v->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</span>
                            </div>
                        </td>
                        <td class="small">{{ $v->asiento->tipoClase->nombre }}</td>
                        <td class="small">{{ $v->metodo_pago }}</td>
                        <td class="text-end fw-bold" style="color:var(--accent)">
                            Bs. {{ number_format($v->monto_total, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:{{ $v->estado === 'Confirmada' ? '#198754' : '#dc3545' }}">
                                {{ $v->estado }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $v->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem"></i>
                            No hay ventas con los filtros seleccionados.
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
                <input type="hidden" name="tipo" value="ventas">
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <input type="hidden" name="metodo_pago" value="{{ request('metodo_pago') }}">
                <input type="hidden" name="clase_id" value="{{ request('clase_id') }}">
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

@if($ventas->count() > 0)
(function() {
    const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#0d6efd';
    new Chart(document.getElementById('chartVentas').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! $chartLabels !!},
            datasets: [
                {
                    label: 'Confirmadas',
                    data: {!! $chartConfirm !!},
                    backgroundColor: '#198754bb',
                    borderColor: '#198754',
                    borderWidth: 2,
                    borderRadius: 6,
                },
                {
                    label: 'Canceladas',
                    data: {!! $chartCancelada !!},
                    backgroundColor: '#dc3545bb',
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
})();
@endif
</script>
@endpush
