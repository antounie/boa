@extends('layouts.app')

@section('titulo', 'Mis Tickets')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-ticket-perforated-fill" style="color:var(--accent)"></i> Mis Tickets</h2>
    <a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-search me-1"></i>Buscar vuelos
    </a>
</div>

@forelse($tickets as $ticket)
@php
    $prog = $ticket->venta->programacionVuelo;
    $emitido = $ticket->estado === 'Emitido';
@endphp
<div class="card shadow-sm mb-3 flight-row">
    <div class="card-body">
        <div class="row align-items-center g-3">

            {{-- Ticket # y estado --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">N° Ticket</div>
                <div class="fw-bold" style="color:var(--accent);font-size:1rem">{{ $ticket->numero_ticket }}</div>
                <span class="badge mt-1" style="background:{{ $emitido ? 'var(--btn-primary-bg)' : '#dc3545' }};font-size:0.72rem">
                    <i class="bi bi-{{ $emitido ? 'check-circle' : 'x-circle' }} me-1"></i>{{ $ticket->estado }}
                </span>
            </div>

            {{-- Vuelo --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Vuelo</div>
                <div class="fw-bold">{{ $prog->codigo_vuelo }}</div>
                <div class="text-muted small">{{ $prog->aeronave->modelo ?? '' }}</div>
            </div>

            {{-- Ruta --}}
            <div class="col-md-3">
                <div class="text-muted small mb-1">Ruta</div>
                @php
                    $oriT = $ticket->subTramo ? $ticket->subTramo->aeropuertoOrigen : $prog->aeropuertoOrigen;
                    $desT = $ticket->subTramo ? $ticket->subTramo->aeropuertoDestino : $prog->aeropuertoDestino;
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <span class="iata-badge">{{ $oriT->codigo_IATA }}</span>
                    <i class="bi bi-arrow-right" style="color:var(--accent)"></i>
                    <span class="iata-badge">{{ $desT->codigo_IATA }}</span>
                </div>
                <div class="text-muted small mt-1">
                    {{ $oriT->ciudad }} → {{ $desT->ciudad }}
                </div>
                @if($ticket->esParcial())
                    <small class="badge bg-warning text-dark mt-1">Tramo parcial</small>
                @endif
            </div>

            {{-- Fecha y hora --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Salida</div>
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($prog->fecha_salida)->format('d M Y') }}</div>
                <div class="text-muted small">{{ \Carbon\Carbon::parse($prog->hora_salida)->format('H:i') }} hrs</div>
                @if($prog->fecha_original)
                <span class="badge bg-warning text-dark mt-1" style="font-size:0.65rem" title="Reprogramado. Original: {{ \Carbon\Carbon::parse($prog->fecha_original)->format('d/m/Y') }} {{ $prog->hora_original }}">
                    <i class="bi bi-calendar-check me-1"></i>Reprogramado
                </span>
                @endif
            </div>

            {{-- Asiento + Pasajero --}}
            <div class="col-md-2">
                <div class="text-muted small mb-1">Asiento / Clase</div>
                <div class="fw-semibold">{{ $ticket->asiento->numero }}</div>
                <span class="badge" style="background:var(--accent);opacity:0.85;font-size:0.72rem">
                    {{ $ticket->asiento->tipoClase->nombre }}
                </span>
                @if($ticket->pasajero_nombre)
                <div class="text-muted mt-1" style="font-size:0.72rem">
                    <i class="bi bi-person me-1"></i>{{ $ticket->pasajero_nombre }} {{ $ticket->pasajero_apellido }}
                </div>
                @endif
            </div>

            {{-- Fecha emisión --}}
            <div class="col-md-1 text-end">
                <div class="text-muted small mb-1">Emitido</div>
                <div class="small">{{ $ticket->created_at->format('d/m/Y') }}</div>
                <div class="text-muted" style="font-size:0.7rem">{{ $ticket->created_at->format('H:i') }}</div>
            </div>

            {{-- Descargar --}}
            <div class="col-12 d-flex justify-content-end mt-2 pt-2 border-top">
                @if($emitido)
                <a href="{{ route('cliente.ticket.pdf', $ticket->id) }}"
                   class="btn btn-sm btn-primary"
                   target="_blank">
                    <i class="bi bi-download me-1"></i>Descargar Boarding Pass
                </a>
                @else
                <span class="text-muted small"><i class="bi bi-x-circle me-1"></i>Ticket no disponible para descarga</span>
                @endif
            </div>

        </div>
    </div>
</div>
@empty
<div class="card shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-ticket-perforated d-block mb-3" style="font-size:3rem;color:var(--accent);opacity:0.3"></i>
        <h5 class="text-muted mb-2">No tienes tickets emitidos</h5>
        <p class="text-muted small mb-3">Compra tu pasaje y aparecerá aquí.</p>
        <a href="{{ route('cliente.buscar') }}" class="btn btn-primary">
            <i class="bi bi-search me-2"></i>Buscar vuelos
        </a>
    </div>
</div>
@endforelse

@if(method_exists($tickets, 'links') && $tickets->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $tickets->links() }}
</div>
@endif

@endsection
