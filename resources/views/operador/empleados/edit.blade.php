@extends('layouts.app')

@section('titulo', 'Editar Empleado')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Empleado: {{ $empleado->nombre }} {{ $empleado->apellido }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.empleados.update', $empleado) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                   id="apellido" name="apellido" value="{{ old('apellido', $empleado->apellido) }}" required>
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cargo" class="form-label">Cargo</label>
                            <select class="form-select @error('cargo') is-invalid @enderror"
                                    id="cargo" name="cargo" required>
                                <option value="Piloto" {{ old('cargo', $empleado->cargo) == 'Piloto' ? 'selected' : '' }}>Piloto</option>
                                <option value="Copiloto" {{ old('cargo', $empleado->cargo) == 'Copiloto' ? 'selected' : '' }}>Copiloto</option>
                                <option value="Auxiliar" {{ old('cargo', $empleado->cargo) == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                            </select>
                            @error('cargo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="licencia" class="form-label">Licencia</label>
                            <input type="text" class="form-control @error('licencia') is-invalid @enderror"
                                   id="licencia" name="licencia" value="{{ old('licencia', $empleado->licencia) }}">
                            @error('licencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado actual</label>
                        <div>
                            @if($empleado->estado === 'Activo')
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Activo</span>
                            @else
                                <span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.empleados.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection