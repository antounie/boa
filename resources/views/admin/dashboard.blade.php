@extends('layouts.app')

@section('titulo', 'Panel Administrador')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Panel del Administrador</h2>
        <p class="text-muted">Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }} ({{ Auth::user()->rol->nombre }})</p>
        <hr>

        <div class="row">
            @if($permisosUsuario->contains('usuarios'))
            <div class="col-md-3 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Usuarios</h6>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-primary">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('clientes'))
            <div class="col-md-3 mb-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-lines-fill text-info" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Clientes</h6>
                        <a href="{{ route('admin.clientes.index') }}" class="btn btn-sm btn-info">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('ventas'))
            <div class="col-md-3 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Ventas</h6>
                        <a href="{{ route('admin.ventas.index') }}" class="btn btn-sm btn-success">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('devoluciones'))
            <div class="col-md-3 mb-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-return-left text-danger" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Devoluciones</h6>
                        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-sm btn-danger">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('ingresos'))
            <div class="col-md-3 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up-arrow text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Ingresos</h6>
                        <a href="{{ route('admin.ingresos.index') }}" class="btn btn-sm btn-success">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('egresos'))
            <div class="col-md-3 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-down-arrow text-warning" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Egresos</h6>
                        <a href="{{ route('admin.egresos.index') }}" class="btn btn-sm btn-warning">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('reportes'))
            <div class="col-md-3 mb-3">
                <div class="card border-dark h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-bar-graph text-dark" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Reportes</h6>
                        <a href="{{ route('admin.reportes.index') }}" class="btn btn-sm btn-dark">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection