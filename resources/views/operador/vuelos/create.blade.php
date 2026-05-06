@extends('layouts.app')

@section('titulo', 'Crear Vuelo')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Registrar Nuevo Vuelo</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('operador.vuelos.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo_vuelo" class="form-label">Código de Vuelo</label>
                        <input type="text" class="form-control @error('codigo_vuelo') is-invalid @enderror"
                               id="codigo_vuelo" name="codigo_vuelo" value="{{ old('codigo_vuelo') }}"
                               style="text-transform: uppercase;" required>
                        @error('codigo_vuelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Ej: OB-101, OB-205</small>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Vuelo</label>
                        <select class="form-select @error('tipo') is-invalid @enderror"
                                id="tipo" name="tipo" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="Directo" {{ old('tipo') == 'Directo' ? 'selected' : '' }}>Directo</option>
                            <option value="ConEscalas" {{ old('tipo') == 'ConEscalas' ? 'selected' : '' }}>Con Escalas</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="divVueloPadre" style="display: none;">
                        <label for="vuelo_padre_id" class="form-label">Vuelo Padre (si es tramo de escala)</label>
                        <select class="form-select @error('vuelo_padre_id') is-invalid @enderror"
                                id="vuelo_padre_id" name="vuelo_padre_id">
                            <option value="">Ninguno (es vuelo principal)</option>
                            @foreach($vuelosPadre as $padre)
                                <option value="{{ $padre->id }}" {{ old('vuelo_padre_id') == $padre->id ? 'selected' : '' }}>
                                    {{ $padre->codigo_vuelo }}
                                </option>
                            @endforeach
                        </select>
                        @error('vuelo_padre_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Solo seleccionar si este vuelo es un tramo de escala de otro vuelo.</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operador.vuelos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar Vuelo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar/ocultar selector de vuelo padre
    document.getElementById('tipo').addEventListener('change', function() {
        const divPadre = document.getElementById('divVueloPadre');
        divPadre.style.display = this.value === 'ConEscalas' ? 'block' : 'none';
    });
    // Verificar al cargar la página
    if (document.getElementById('tipo').value === 'ConEscalas') {
        document.getElementById('divVueloPadre').style.display = 'block';
    }
</script>
@endpush