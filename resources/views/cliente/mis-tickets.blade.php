@extends('layouts.app')

@section('titulo', 'Mis Tickets')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-ticket-perforated"></i> Mis Tickets</h2>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Ticket</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Fecha Vuelo</th>
                            <th>Asiento</th>
                            <th>Clase</th>
                            <th>Estado</th>
                            <th>Emitido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td><strong class="text-primary">{{ $ticket->numero_ticket }}</strong></td>
                            <td>{{ $ticket->venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $ticket->venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ticket->venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $ticket->venta->programacionVuelo->fecha_salida }} {{ $ticket->venta->programacionVuelo->hora_salida }}</td>
                            <td>{{ $ticket->venta->asiento->numero }}</td>
                            <td><span class="badge bg-info">{{ $ticket->venta->asiento->tipoClase->nombre }}</span></td>
                            <td>
                                <span class="badge bg-{{ $ticket->estado === 'Emitido' ? 'success' : 'danger' }}">
                                    {{ $ticket->estado }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No tiene tickets emitidos.
                                <br><a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm mt-2">Buscar Vuelos</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection