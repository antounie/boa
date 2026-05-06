@extends('layouts.app')

@section('titulo', 'Gestionar Tipo de Clases')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-star"></i> Gestionar Tipo de Clases</h2>
            <a href="{{ route('operador.tipo-clases.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Clase
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Características</th>
                            <th class="text-center">Asientos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipoClases as $clase)
                        <tr>
                            <td>{{ $clase->id }}</td>
                            <td><strong>{{ $clase->nombre }}</strong></td>
                            <td>{{ $clase->descripcion ?? 'Sin descripción' }}</td>
                            <td>{{ Str::limit($clase->caracteristicas, 50) ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $clase->asientos_count }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('operador.tipo-clases.edit', $clase) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('operador.tipo-clases.destroy', $clase) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar la clase {{ $clase->nombre }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron tipos de clase.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $tipoClases->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection