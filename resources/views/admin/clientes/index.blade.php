@extends('layouts.app')

@section('titulo', 'Gestionar Clientes')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-person-lines-fill"></i> Gestionar Clientes</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.clientes.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar por nombre, apellido, documento o email...">
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
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th class="text-center">Reservas</th>
                            <th class="text-center">Compras</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td><strong>{{ $cliente->nombre }} {{ $cliente->apellido }}</strong></td>
                            <td>{{ $cliente->documento_identidad }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td class="text-center"><span class="badge bg-info">{{ $cliente->reservas_count }}</span></td>
                            <td class="text-center"><span class="badge bg-success">{{ $cliente->ventas_count }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver historial">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No se encontraron clientes.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $clientes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection