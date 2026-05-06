@extends('layouts.app')

@section('titulo', 'Panel Cliente')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Panel del Cliente</h2>
        <p class="text-muted">Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
        <hr>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-search text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Buscar Vuelos</h5>
                        <p class="text-muted">Encuentra vuelos disponibles</p>
                        <a href="{{ route('cliente.buscar') }}" class="btn btn-primary">Buscar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bookmark text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Mis Reservas</h5>
                        <p class="text-muted">Consulta tus reservas</p>
                        <a href="{{ route('cliente.mis.reservas') }}" class="btn btn-success">Ver Reservas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-perforated text-info" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Mis Tickets</h5>
                        <p class="text-muted">Consulta tus tickets de abordaje</p>
                        <a href="{{ route('cliente.mis.tickets') }}" class="btn btn-info">Ver Tickets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection