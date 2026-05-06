@extends('layouts.app')

@section('titulo', 'Panel Operador')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Panel del Operador</h2>
        <p class="text-muted">Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }} ({{ Auth::user()->rol->nombre }})</p>
        <hr>

        <div class="row">
            @if($permisosUsuario->contains('aeropuertos'))
            <div class="col-md-3 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt text-primary" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Aeropuertos</h6>
                        <a href="{{ route('operador.aeropuertos.index') }}" class="btn btn-sm btn-primary">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('aeronaves'))
            <div class="col-md-3 mb-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-airplane-engines text-info" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Aeronaves</h6>
                        <a href="{{ route('operador.aeronaves.index') }}" class="btn btn-sm btn-info">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('vuelos'))
            <div class="col-md-3 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-airplane text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Vuelos</h6>
                        <a href="{{ route('operador.vuelos.index') }}" class="btn btn-sm btn-success">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('programacion_vuelos'))
            <div class="col-md-3 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar3 text-warning" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Programación</h6>
                        <a href="{{ route('operador.programaciones.index') }}" class="btn btn-sm btn-warning">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('empleados'))
            <div class="col-md-3 mb-3">
                <div class="card border-dark h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge text-dark" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Empleados</h6>
                        <a href="{{ route('operador.empleados.index') }}" class="btn btn-sm btn-dark">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif

            @if($permisosUsuario->contains('salidas'))
            <div class="col-md-3 mb-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box-arrow-right text-danger" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Salidas</h6>
                        <a href="{{ route('operador.salidas.index') }}" class="btn btn-sm btn-danger">Gestionar</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection