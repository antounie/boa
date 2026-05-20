@extends('layouts.app')

@section('titulo', 'Reporte de Vuelos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="bi bi-airplane me-2" style="color:var(--accent)"></i>Reporte de Vuelos</h2>
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
                <div class="text-muted small mb-1">Total vuelos</div>
                <div class="kpi-value">{{ $totales['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Programados</div>
                <div class="kpi-value" style="color:#0d6efd">{{ $totales['programados'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Salidos</div>
                <div class="kpi-value" style="color:#198754">{{ $totales['salidos'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Asientos vendidos</div>
                <div class="kpi-value">{{ $totales['vendidos'] }}</div>
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

        <form id="formFiltros" method="GET" action="{{ route('admin.reportes.vuelos') }}" class="row g-2">
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
                    <option value="Programado" {{ request('estado') === 'Programado' ? 'selected' : '' }}>Programado</option>
                    <option value="Completo"   {{ request('estado') === 'Completo'   ? 'selected' : '' }}>Completo</option>
                    <option value="Salido"     {{ request('estado') === 'Salido'     ? 'selected' : '' }}>Salido</option>
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
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Aeronave</label>
                <select name="aeronave_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($aeronaves as $av)
                        <option value="{{ $av->id }}" {{ request('aeronave_id') == $av->id ? 'selected' : '' }}>
                            {{ $av->matricula }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 align-items-center flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="{{ route('admin.reportes.vuelos') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('admin.reportes.vuelos.pdf', request()->query()) }}"
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
@if($programaciones->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold"><i class="bi bi-bar-chart me-2"></i>Vuelos por mes</div>
    <div class="card-body">
        <canvas id="chartVuelos" height="80"></canvas>
    </div>
</div>
@endif

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table me-2"></i>Detalle</span>
        <span class="badge" style="background:var(--btn-primary-bg)">{{ $programaciones->count() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Código vuelo</th>
                        <th>Ruta</th>
                        <th>Aeronave</th>
                        <th>Fecha salida</th>
                        <th class="text-center">Precio base</th>
                        <th class="text-center">Vendidos</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programaciones as $p)
                    <tr>
                        <td><strong style="color:var(--accent)">{{ $p->codigo_vuelo }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="iata-badge">{{ $p->aeropuertoOrigen->codigo_IATA }}</span>
                                <i class="bi bi-arrow-right text-muted small"></i>
                                <span class="iata-badge">{{ $p->aeropuertoDestino->codigo_IATA }}</span>
                            </div>
                        </td>
                        <td>{{ $p->aeronave->matricula }}</td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($p->fecha_salida)->format('d M Y') }}</div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($p->hora_salida)->format('H:i') }}</div>
                        </td>
                        <td class="text-center">Bs. {{ number_format($p->precio_base, 2) }}</td>
                        <td class="text-center">{{ $p->asientos_vendidos }}/{{ $p->aeronave->capacidad_total }}</td>
                        <td class="text-center">
                            @php $colores = ['Programado'=>'#0d6efd','Completo'=>'#6c757d','Salido'=>'#198754']; @endphp
                            <span class="badge" style="background:{{ $colores[$p->estado] ?? '#6c757d' }}">
                                {{ $p->estado }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem"></i>
                            No hay vuelos con los filtros seleccionados.
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
                <input type="hidden" name="tipo" value="vuelos">
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <input type="hidden" name="aeropuerto_origen" value="{{ request('aeropuerto_origen') }}">
                <input type="hidden" name="aeropuerto_destino" value="{{ request('aeropuerto_destino') }}">
                <input type="hidden" name="aeronave_id" value="{{ request('aeronave_id') }}">
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

@if($programaciones->count() > 0)
(function() {
    const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#0d6efd';
    new Chart(document.getElementById('chartVuelos').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! $chartLabels !!},
            datasets: [{
                label: 'Vuelos',
                data: {!! $chartData !!},
                backgroundColor: accent + 'bb',
                borderColor: accent,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
})();
@endif
</script>
@endpush
