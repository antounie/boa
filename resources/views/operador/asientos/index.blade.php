@extends('layouts.app')

@section('titulo', 'Gestionar Asientos')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-grid-3x3"></i> Gestionar Asientos</h2>
        <p class="text-muted">Seleccione una aeronave para configurar sus asientos.</p>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.asientos.index') }}" class="row g-3">
                    <div class="col-md-8">
                        <select class="form-select" name="aeronave_id" onchange="this.form.submit()">
                            <option value="">Seleccionar aeronave...</option>
                            @foreach($aeronaves as $aeronave)
                                <option value="{{ $aeronave->id }}" {{ request('aeronave_id') == $aeronave->id ? 'selected' : '' }}>
                                    {{ $aeronave->matricula }} - {{ $aeronave->modelo }}
                                    ({{ $aeronave->asientos_count }}/{{ $aeronave->capacidad_total }} asientos)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">Ver Asientos</button>
                    </div>
                </form>
            </div>
        </div>

        @if($aeronaveSeleccionada)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-airplane-engines"></i>
                    {{ $aeronaveSeleccionada->matricula }} - {{ $aeronaveSeleccionada->modelo }}
                    ({{ $asientos->count() }}/{{ $aeronaveSeleccionada->capacidad_total }} asientos)
                </h5>
                <div>
                    @if($asientos->count() > 0)
                    <form action="{{ route('operador.asientos.eliminar-todos') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="aeronave_id" value="{{ $aeronaveSeleccionada->id }}">
                        <button type="submit" class="btn btn-sm btn-danger me-2"
                                onclick="return confirm('¿Está seguro de eliminar TODOS los {{ $asientos->count() }} asientos de esta aeronave?')">
                            <i class="bi bi-trash"></i> Eliminar Todos
                        </button>
                    </form>
                    @endif
                    <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal" data-bs-target="#modalMasivo">
                        <i class="bi bi-lightning"></i> Generar Masivo
                    </button>
                    <a href="{{ route('operador.asientos.create', ['aeronave_id' => $aeronaveSeleccionada->id]) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-plus-lg"></i> Agregar Individual
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                @if($asientos->count() > 0)
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Número</th>
                            <th>Fila</th>
                            <th>Clase</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($asientos as $asiento)
                        <tr>
                            <td><strong>{{ $asiento->numero }}</strong></td>
                            <td>{{ $asiento->fila }}</td>
                            <td><span class="badge bg-info">{{ $asiento->tipoClase->nombre }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('operador.asientos.edit', $asiento) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.asientos.destroy', $asiento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar el asiento {{ $asiento->numero }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox"></i> Esta aeronave no tiene asientos configurados.
                </div>
                @endif
            </div>
        </div>

        {{-- Modal Generación Masiva --}}
        <div class="modal fade" id="modalMasivo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('operador.asientos.generar-masivo') }}">
                        @csrf
                        <input type="hidden" name="aeronave_id" value="{{ $aeronaveSeleccionada->id }}">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="bi bi-lightning"></i> Generar Asientos Masivamente</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Capacidad total:</strong> {{ $aeronaveSeleccionada->capacidad_total }} |
                                <strong>Configurados:</strong> {{ $asientos->count() }} |
                                <strong>Disponibles:</strong> {{ $aeronaveSeleccionada->capacidad_total - $asientos->count() }}
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Columnas por fila (letras)</label>
                                <input type="text" class="form-control" name="columnas" required placeholder="Ej: ABCDEF" style="text-transform: uppercase;">
                                <small class="text-muted">Cada letra es un asiento por fila. Ej: ABCDEF = 6 asientos por fila</small>
                            </div>

                            <hr>
                            <h6><i class="bi bi-star"></i> Configuración por Clase</h6>
                            <p class="text-muted small">Defina el rango de filas para cada tipo de clase. Las filas que no se asignen no se generarán.</p>

                            <div id="clasesContainer">
                                @foreach(\App\Models\TipoClase::all() as $index => $clase)
                                <div class="card mb-3 border-primary">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input clase-check" type="checkbox" id="clase_{{ $clase->id }}" name="clases[{{ $clase->id }}][activo]" value="1" {{ $index === 0 ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="clase_{{ $clase->id }}">
                                                {{ $clase->nombre }}
                                            </label>
                                        </div>
                                        <span class="badge bg-info">{{ $clase->descripcion }}</span>
                                    </div>
                                    <div class="card-body clase-body" id="body_{{ $clase->id }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Desde fila</label>
                                                <input type="number" class="form-control" name="clases[{{ $clase->id }}][fila_inicio]" min="1" placeholder="Ej: 1">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Hasta fila</label>
                                                <input type="number" class="form-control" name="clases[{{ $clase->id }}][fila_fin]" min="1" placeholder="Ej: 5">
                                            </div>
                                        </div>
                                        <small class="text-muted">Ejemplo: Fila 1 a 5 = 5 filas de clase {{ $clase->nombre }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="alert alert-warning" id="resumenGeneracion" style="display:none;">
                                <strong>Total a generar:</strong> <span id="totalAsientos">0</span> asientos
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-lightning"></i> Generar Asientos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            // Mostrar/ocultar cuerpo de cada clase
            document.querySelectorAll('.clase-check').forEach(function(check) {
                check.addEventListener('change', function() {
                    const claseId = this.id.replace('clase_', '');
                    const body = document.getElementById('body_' + claseId);
                    body.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        body.querySelectorAll('input[type="number"]').forEach(inp => inp.value = '');
                    }
                    calcularTotal();
                });
            });

            // Calcular total de asientos a generar
            function calcularTotal() {
                const columnas = document.querySelector('input[name="columnas"]').value.length;
                let totalFilas = 0;

                document.querySelectorAll('.clase-check:checked').forEach(function(check) {
                    const claseId = check.id.replace('clase_', '');
                    const inicio = parseInt(document.querySelector(`input[name="clases[${claseId}][fila_inicio]"]`).value) || 0;
                    const fin = parseInt(document.querySelector(`input[name="clases[${claseId}][fila_fin]"]`).value) || 0;
                    if (inicio > 0 && fin >= inicio) {
                        totalFilas += (fin - inicio + 1);
                    }
                });

                const total = totalFilas * columnas;
                const resumen = document.getElementById('resumenGeneracion');
                const totalSpan = document.getElementById('totalAsientos');

                if (total > 0) {
                    resumen.style.display = 'block';
                    totalSpan.textContent = total;
                } else {
                    resumen.style.display = 'none';
                }
            }

            document.querySelector('input[name="columnas"]').addEventListener('input', calcularTotal);
            document.querySelectorAll('input[type="number"]').forEach(inp => inp.addEventListener('input', calcularTotal));
        </script>
        @endpush
        @endif
    </div>
</div>
@endsection