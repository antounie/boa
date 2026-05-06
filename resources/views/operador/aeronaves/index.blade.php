@extends('layouts.app')

@section('titulo', 'Gestionar Aeronaves')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-airplane-engines"></i> Gestionar Aeronaves</h2>
            <a href="{{ route('operador.aeronaves.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Aeronave
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.aeronaves.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por matrícula, modelo o fabricante...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Matrícula</th>
                            <th>Modelo</th>
                            <th>Fabricante</th>
                            <th class="text-center">Capacidad</th>
                            <th class="text-center">Asientos Config.</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aeronaves as $aeronave)
                        <tr>
                            <td>{{ $aeronave->id }}</td>
                            <td><strong class="text-primary">{{ $aeronave->matricula }}</strong></td>
                            <td>{{ $aeronave->modelo }}</td>
                            <td>{{ $aeronave->fabricante }}</td>
                            <td class="text-center">{{ $aeronave->capacidad_total }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $aeronave->asientos_count }}</span>
                            </td>
                            <td>
                                @if($aeronave->estado === 'Activa')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactiva</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.aeronaves.edit', $aeronave) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.aeronaves.toggle-status', $aeronave) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($aeronave->estado === 'Activa')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Dar de Baja"
                                                onclick="return confirm('¿Está seguro de dar de baja la aeronave {{ $aeronave->matricula }}?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success" title="Reactivar"
                                                onclick="return confirm('¿Está seguro de reactivar la aeronave {{ $aeronave->matricula }}?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron aeronaves.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $aeronaves->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection