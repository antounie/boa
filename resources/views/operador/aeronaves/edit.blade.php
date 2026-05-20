@extends('layouts.app')

@section('titulo', 'Editar Aeronave')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-9">

        {{-- Datos generales --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Aeronave: {{ $aeronave->matricula }}</h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('operador.aeronaves.update', $aeronave) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Matrícula</label>
                            <input type="text" class="form-control @error('matricula') is-invalid @enderror"
                                   name="matricula" value="{{ old('matricula', $aeronave->matricula) }}"
                                   style="text-transform:uppercase" required>
                            @error('matricula')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                                   name="modelo" value="{{ old('modelo', $aeronave->modelo) }}" required>
                            @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fabricante</label>
                            <input type="text" class="form-control @error('fabricante') is-invalid @enderror"
                                   name="fabricante" value="{{ old('fabricante', $aeronave->fabricante) }}" required>
                            @error('fabricante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Capacidad total</label>
                            <input type="number" class="form-control @error('capacidad_total') is-invalid @enderror"
                                   name="capacidad_total"
                                   value="{{ old('capacidad_total', $aeronave->capacidad_total) }}"
                                   min="{{ $totalAsientosConfigurados }}" required>
                            <small class="text-muted">Mínimo {{ $totalAsientosConfigurados }} (asientos ya configurados).</small>
                            @error('capacidad_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <div class="mt-1">
                                @if($aeronave->estado === 'Activa')
                                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Activa</span>
                                @else
                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Inactiva</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg"></i> Guardar cambios de datos
                    </button>
                </form>
            </div>
        </div>

        {{-- Asientos actuales --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-grid-3x3"></i> Configuración actual de asientos</h6>
            </div>
            <div class="card-body">
                @if($resumenAsientos->isEmpty())
                    <div class="alert alert-warning">Esta aeronave aún no tiene asientos configurados.</div>
                @else
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Clase</th>
                            <th class="text-center">Cantidad</th>
                            <th>Asientos (vista previa)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resumenAsientos as $resumen)
                        <tr>
                            <td><strong>{{ $resumen['tipoClase']->nombre }}</strong></td>
                            <td class="text-center">{{ $resumen['cantidad'] }}</td>
                            <td>
                                @php
                                    $asientosClase = \App\Models\Asiento::where('aeronave_id', $aeronave->id)
                                        ->where('tipo_clase_id', $resumen['tipoClase']->id)
                                        ->orderBy('fila')->orderBy('numero')
                                        ->take(10)->pluck('numero');
                                @endphp
                                <small class="text-muted">
                                    {{ $asientosClase->join(', ') }}{{ $resumen['cantidad'] > 10 ? '...' : '' }}
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Agregar más asientos --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-plus-lg"></i> Agregar más asientos</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Los nuevos asientos continuarán desde la fila <strong>{{ $ultimaFila + 1 }}</strong>.
                    Se agregarán automáticamente a todas las programaciones activas de esta aeronave.
                </p>

                <form method="POST" action="{{ route('operador.aeronaves.update', $aeronave) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="matricula" value="{{ $aeronave->matricula }}">
                    <input type="hidden" name="modelo" value="{{ $aeronave->modelo }}">
                    <input type="hidden" name="fabricante" value="{{ $aeronave->fabricante }}">
                    {{-- capacidad_total will be auto-recalculated after adding; pass current value to pass validation --}}
                    <input type="hidden" name="capacidad_total" value="{{ $aeronave->capacidad_total }}">

                    <table class="table table-bordered" id="tablaClasesEdit">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Clase</th>
                                <th style="width:130px">Cantidad</th>
                                <th style="width:160px">Columnas por fila</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="filaClasesEdit">
                            <tr>
                                <td>
                                    <select class="form-select" name="clases[0][tipo_clase_id]" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($tipoClases as $tc)
                                        <option value="{{ $tc->id }}">{{ $tc->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control cantidad-edit"
                                           name="clases[0][cantidad]" min="1" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control"
                                           name="clases[0][columnas]" min="1" max="10" value="3"
                                           title="Ej: 6 = columnas A,B,C,D,E,F" required>
                                    <small class="text-muted">Letras A–J</small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-quitar-edit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarEdit">
                                        <i class="bi bi-plus-lg"></i> Agregar otra clase
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar asientos
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('operador.aeronaves.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<script>
const opcionesClase = `@foreach($tipoClases as $tc)<option value="{{ $tc->id }}">{{ $tc->nombre }}</option>@endforeach`;
let indiceEdit = 1;

document.getElementById('btnAgregarEdit').addEventListener('click', function () {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select class="form-select" name="clases[${indiceEdit}][tipo_clase_id]" required>
                <option value="">Seleccionar...</option>${opcionesClase}
            </select>
        </td>
        <td><input type="number" class="form-control cantidad-edit" name="clases[${indiceEdit}][cantidad]" min="1" required></td>
        <td>
            <input type="number" class="form-control" name="clases[${indiceEdit}][columnas]" min="1" max="10" value="3" required>
            <small class="text-muted">Letras A–J</small>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-quitar-edit"><i class="bi bi-trash"></i></button>
        </td>`;
    document.getElementById('filaClasesEdit').appendChild(tr);
    tr.querySelector('.btn-quitar-edit').addEventListener('click', () => tr.remove());
    indiceEdit++;
});

document.querySelectorAll('.btn-quitar-edit').forEach(btn =>
    btn.addEventListener('click', () => btn.closest('tr').remove())
);
</script>
@endsection
