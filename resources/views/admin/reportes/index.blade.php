@extends('layouts.app')

@section('titulo', 'Reportes')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Módulo de Reportes</h2>
        <p class="text-muted">Seleccione el tipo de reporte que desea generar.</p>
        <hr>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-airplane text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Reporte de Vuelos</h5>
                        <p class="text-muted">Programaciones de vuelo por fecha y estado</p>
                        <a href="{{ route('admin.reportes.vuelos') }}" class="btn btn-primary">Generar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Reporte de Ventas</h5>
                        <p class="text-muted">Ventas de pasajes por fecha y estado</p>
                        <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-success">Generar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up-arrow text-info" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Reporte de Ingresos</h5>
                        <p class="text-muted">Ingresos financieros por fecha</p>
                        <a href="{{ route('admin.reportes.ingresos') }}" class="btn btn-info">Generar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-down-arrow text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Reporte de Egresos</h5>
                        <p class="text-muted">Egresos por devoluciones por fecha</p>
                        <a href="{{ route('admin.reportes.egresos') }}" class="btn btn-danger">Generar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection