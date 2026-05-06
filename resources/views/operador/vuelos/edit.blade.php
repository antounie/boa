@extends('layouts.app')

@section('titulo', 'Editar Vuelo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Vuelo: {{ $vuelo->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.vuelos.update', $vuelo) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="codigo_vuelo" class="form-label">Código de Vuelo</label>
                        <input type="text" class="form-control @error('codigo_vuelo') is-invalid @enderror"
                               id="codigo_vuelo" name="codigo_vuelo" value="{{ old('codigo_vuelo', $vuelo->codigo_vuelo) }}"
                               style="text-transform: uppercase;" required>
                        @error('codigo_vuelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Vuelo</label>
                        <select class="form-select @error('tipo') is-invalid @enderror"
                                id="tipo" name="tipo" required>
                            <option value="Directo" {{ old('tipo', $vuelo->tipo) == 'Directo' ? 'selected' : '' }}>Directo</option>
                            <option value="ConEscalas" {{ old('tipo', $vuelo->tipo) == 'ConEscalas' ? 'selected' : '' }}>Con Escalas</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado actual</label>
                        <div>
                            @if($vuelo->estado === 'Activo')
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Activo</span>
                            @else
                                <span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Cancelado</span>
                            @endif
                        </div>
                    </div>

                    @if($vuelo->escalas->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Escalas (tramos hijos)</label>
                        <ul class="list-group">
                            @foreach($vuelo->escalas as $escala)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $escala->codigo_vuelo }}
                                <span class="badge bg-{{ $escala->estado === 'Activo' ? 'success' : 'danger' }}">{{ $escala->estado }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.vuelos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Vuelo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection