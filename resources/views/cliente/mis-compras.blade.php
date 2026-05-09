@extends('layouts.app')

@section('titulo', 'Mis Compras')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-cart-check-fill" style="color:var(--accent)"></i> Mis Compras</h2>
    <a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-search me-1"></i>Buscar vuelos
    </a>
</div>

@forelse($ventas as $venta)
@php
    $prog = $venta->programacionVuelo;
    $confirmada = $venta->estado === 'Confirmada';
@endphp
<div class="card shadow-sm mb-3 flight-row">
    <div class="card-body">
        <div class="row align-items-center g-3">

            {{-- Código de venta --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Código</div>
                <div class="fw-bold" style="color:var(--accent);font-size:0.9rem">{{ $venta->codigo_venta }}</div>
                <span class="badge mt-1" style="background:{{ $confirmada ? 'var(--btn-primary-bg)' : '#dc3545' }};font-size:0.72rem">
                    <i class="bi bi-{{ $confirmada ? 'check-circle' : 'x-circle' }} me-1"></i>{{ $venta->estado }}
                </span>
            </div>

            {{-- Vuelo --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Vuelo</div>
                <div class="fw-bold">{{ $prog->vuelo->codigo_vuelo }}</div>
                <div class="text-muted small">{{ $venta->metodo_pago }}</div>
            </div>

            {{-- Ruta --}}
            <div class="col-md-3">
                <div class="text-muted small mb-1">Ruta</div>
                <div class="d-flex align-items-center gap-2">
                    <span class="iata-badge">{{ $prog->ruta->aeropuertoOrigen->codigo_IATA }}</span>
                    <i class="bi bi-arrow-right" style="color:var(--accent)"></i>
                    <span class="iata-badge">{{ $prog->ruta->aeropuertoDestino->codigo_IATA }}</span>
                </div>
                <div class="text-muted small mt-1">
                    {{ $prog->ruta->aeropuertoOrigen->ciudad }} → {{ $prog->ruta->aeropuertoDestino->ciudad }}
                </div>
            </div>

            {{-- Fecha vuelo --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Fecha vuelo</div>
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($prog->fecha_salida)->format('d M Y') }}</div>
                <div class="text-muted small">{{ \Carbon\Carbon::parse($prog->hora_salida)->format('H:i') }} hrs</div>
            </div>

            {{-- Asiento --}}
            <div class="col-md-1">
                <div class="text-muted small mb-1">Asiento</div>
                <div class="fw-semibold">{{ $venta->asiento->numero }}</div>
                <div class="text-muted small" style="font-size:0.72rem">{{ $venta->asiento->tipoClase->nombre }}</div>
            </div>

            {{-- Monto + Ticket --}}
            <div class="col-md-2 text-end">
                <div class="text-muted small mb-1">Monto</div>
                <div class="fw-bold" style="color:var(--accent);font-size:1.1rem">
                    Bs. {{ number_format($venta->monto_total, 2) }}
                </div>
                @if($venta->ticket)
                    <span class="badge mt-1" style="background:var(--btn-primary-bg);font-size:0.7rem">
                        <i class="bi bi-ticket me-1"></i>{{ $venta->ticket->numero_ticket }}
                    </span>
                @endif
            </div>

        </div>
    </div>
</div>
@empty
<div class="card shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-cart d-block mb-3" style="font-size:3rem;color:var(--accent);opacity:0.3"></i>
        <h5 class="text-muted mb-2">No tienes compras registradas</h5>
        <p class="text-muted small mb-3">Reserva y compra tu primer pasaje.</p>
        <a href="{{ route('cliente.buscar') }}" class="btn btn-primary">
            <i class="bi bi-search me-2"></i>Buscar vuelos
        </a>
    </div>
</div>
@endforelse

@if(method_exists($ventas, 'links') && $ventas->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $ventas->links() }}
</div>
@endif

@endsection
