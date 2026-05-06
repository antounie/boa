@extends('layouts.app')

@section('titulo', 'Gestionar Permisos')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-key"></i> Gestionar Permisos</h2>
        <p class="text-muted">Administre los permisos de acceso para cada rol del sistema.</p>

        <div class="row">
            @foreach($roles as $rol)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-shield-lock"></i> {{ $rol->nombre }}</h5>
                        <a href="{{ route('admin.permisos.edit', $rol) }}" class="btn btn-sm btn-light">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Módulo</th>
                                    <th class="text-center">Acceso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tablas as $tabla)
                                <tr>
                                    <td>{{ $tabla }}</td>
                                    <td class="text-center">
                                        @if(isset($rol->permisos_data[$tabla]) && $rol->permisos_data[$tabla])
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection