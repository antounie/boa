@extends('layouts.app')

@section('titulo', 'Mi Panel')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')

{{-- Bienvenida --}}
<div class="dash-welcome">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1">
                Hola, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,0.8)">
                <span class="badge me-2" style="background:rgba(255,255,255,0.2)">
                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->rol->nombre }}
                </span>
                <span style="font-size:0.85rem">¡Listo para tu próximo vuelo!</span>
            </p>
        </div>
        <i class="bi bi-ticket-perforated-fill d-none d-md-block" style="font-size:3.5rem;opacity:0.25;color:#fff"></i>
    </div>
</div>

{{-- Accesos rápidos --}}
<div class="row g-3">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card module-card shadow-sm h-100">
            <div class="module-card-icon" style="background: linear-gradient(135deg,#1a5276,#2980b9)">
                <i class="bi bi-search"></i>
            </div>
            <div class="card-body text-center">
                <h6>Buscar Vuelos</h6>
                <p class="card-text">Encuentra y reserva tu próximo vuelo</p>
                <a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Buscar ahora
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <div class="card module-card shadow-sm h-100">
            <div class="module-card-icon" style="background: linear-gradient(135deg,#00695C,#26A69A)">
                <i class="bi bi-bookmark-fill"></i>
            </div>
            <div class="card-body text-center">
                <h6>Mis Reservas</h6>
                <p class="card-text">Consulta y gestiona tus reservas activas</p>
                <a href="{{ route('cliente.mis.reservas') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-bookmark me-1"></i>Ver reservas
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <div class="card module-card shadow-sm h-100">
            <div class="module-card-icon" style="background: linear-gradient(135deg,#1B5E20,#43A047)">
                <i class="bi bi-cart-check-fill"></i>
            </div>
            <div class="card-body text-center">
                <h6>Mis Compras</h6>
                <p class="card-text">Historial de todos tus pasajes comprados</p>
                <a href="{{ route('cliente.mis.compras') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-cart me-1"></i>Ver compras
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <div class="card module-card shadow-sm h-100">
            <div class="module-card-icon" style="background: linear-gradient(135deg,#4A148C,#7B1FA2)">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
            <div class="card-body text-center">
                <h6>Mis Tickets</h6>
                <p class="card-text">Tus tarjetas de embarque digitales</p>
                <a href="{{ route('cliente.mis.tickets') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-ticket me-1"></i>Ver tickets
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
