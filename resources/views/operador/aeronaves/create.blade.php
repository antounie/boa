@extends('layouts.app')

@section('titulo', 'Registrar Aeronave')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Registrar Nueva Aeronave</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.aeronaves.store') }}" id="formAeronave">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control @error('matricula') is-invalid @enderror"
                                   id="matricula" name="matricula" value="{{ old('matricula') }}"
                                   style="text-transform:uppercase" required>
                            @error('matricula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ej: CP-2901</small>
                        </div>
                        <div class="col-md-4">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                                   id="modelo" name="modelo" value="{{ old('modelo') }}" required>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ej: Boeing 737-300</small>
                        </div>
                        <div class="col-md-4">
                            <label for="fabricante" class="form-label">Fabricante</label>
                            <input type="text" class="form-control @error('fabricante') is-invalid @enderror"
                                   id="fabricante" name="fabricante" value="{{ old('fabricante') }}" required>
                            @error('fabricante')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-grid-3x3"></i> Configuración de Asientos por Clase</h6>
                    @error('clases')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <table class="table table-bordered" id="tablaClases">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Clase</th>
                                <th style="width:130px">Cantidad</th>
                                <th style="width:130px">Columnas por fila</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="filaClases">
                            @if(old('clases'))
                                @foreach(old('clases') as $i => $c)
                                <tr>
                                    <td>
                                        <select class="form-select" name="clases[{{ $i }}][tipo_clase_id]" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($tipoClases as $tc)
                                            <option value="{{ $tc->id }}" {{ $c['tipo_clase_id'] == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control cantidad-asientos"
                                               name="clases[{{ $i }}][cantidad]" min="1" value="{{ $c['cantidad'] }}" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control"
                                               name="clases[{{ $i }}][columnas]" min="1" max="10" value="{{ $c['columnas'] ?? 3 }}"
                                               title="Letras A-J según columnas. Ej: 3 = A,B,C" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-quitar-fila"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
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
                                    <input type="number" class="form-control cantidad-asientos"
                                           name="clases[0][cantidad]" min="1" value="" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control"
                                           name="clases[0][columnas]" min="1" max="10" value="3"
                                           title="Letras A-J según columnas. Ej: 3 = A,B,C" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-quitar-fila"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarClase">
                                        <i class="bi bi-plus-lg"></i> Agregar clase
                                    </button>
                                    <span class="ms-3 text-muted">Total asientos: <strong id="totalAsientos">0</strong></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.aeronaves.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Aeronave y Generar Asientos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const tipoClasesOptions = `@foreach($tipoClases as $tc)<option value="{{ $tc->id }}">{{ $tc->nombre }}</option>@endforeach`;

let indice = {{ old('clases') ? count(old('clases')) : 1 }};

function actualizarTotal() {
    let total = 0;
    document.querySelectorAll('.cantidad-asientos').forEach(i => total += parseInt(i.value) || 0);
    document.getElementById('totalAsientos').textContent = total;
}

document.getElementById('btnAgregarClase').addEventListener('click', function () {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select class="form-select" name="clases[${indice}][tipo_clase_id]" required>
                <option value="">Seleccionar...</option>
                ${tipoClasesOptions}
            </select>
        </td>
        <td>
            <input type="number" class="form-control cantidad-asientos"
                   name="clases[${indice}][cantidad]" min="1" required>
        </td>
        <td>
            <input type="number" class="form-control"
                   name="clases[${indice}][columnas]" min="1" max="10" value="3"
                   title="Letras A-J según columnas" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-quitar-fila">
                <i class="bi bi-trash"></i>
            </button>
        </td>`;
    document.getElementById('filaClases').appendChild(tr);
    tr.querySelector('.cantidad-asientos').addEventListener('input', actualizarTotal);
    tr.querySelector('.btn-quitar-fila').addEventListener('click', function () {
        tr.remove();
        actualizarTotal();
    });
    indice++;
});

document.querySelectorAll('.cantidad-asientos').forEach(i => i.addEventListener('input', actualizarTotal));
document.querySelectorAll('.btn-quitar-fila').forEach(btn => btn.addEventListener('click', function () {
    btn.closest('tr').remove();
    actualizarTotal();
}));

actualizarTotal();
</script>
@endsection
