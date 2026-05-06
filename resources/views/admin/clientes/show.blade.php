@extends('layouts.app')

@section('titulo', 'Historial del Cliente')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Cliente: {{ $cliente->nombre }} {{ $cliente->apellido }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Documento:</strong>
                        <p>{{ $cliente->documento_identidad }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Nacimiento:</strong>
                        <p>{{ $cliente->fecha_nacimiento }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Email:</strong>
                        <p>{{ $cliente->email }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Teléfono:</strong>
                        <p>{{ $cliente->telefono ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de Reservas --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-bookmark"></i> Historial de Reservas ({{ $cliente->reservas->count() }})</h5>
            </div>
            <div class="card-body table-responsive">
                @if($cliente->reservas->count() > 0)
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Asiento</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cliente->reservas as $reserva)
                        <tr>
                            <td><strong>{{ $reserva->codigo_reserva }}</strong></td>
                            <td>{{ $reserva->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $reserva->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $reserva->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $reserva->asiento->numero }} ({{ $reserva->asiento->tipoClase->nombre }})</td>
                            <td>${{ number_format($reserva->monto, 2) }}</td>
                            <td>{{ $reserva->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $reserva->estado === 'Confirmada' ? 'success' : 'danger' }}">
                                    {{ $reserva->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center py-3">Sin reservas registradas.</p>
                @endif
            </div>
        </div>

        {{-- Historial de Compras --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Historial de Compras ({{ $cliente->ventas->count() }})</h5>
            </div>
            <div class="card-body table-responsive">
                @if($cliente->ventas->count() > 0)
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Asiento</th>
                            <th>Monto</th>
                            <th>Pago</th>
                            <th>Ticket</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cliente->ventas as $venta)
                        <tr>
                            <td><strong>{{ $venta->codigo_venta }}</strong></td>
                            <td>{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</td>
                            <td>${{ number_format($venta->monto_total, 2) }}</td>
                            <td>{{ $venta->metodo_pago }}</td>
                            <td>
                                @if($venta->ticket)
                                    <span class="badge bg-{{ $venta->ticket->estado === 'Emitido' ? 'primary' : 'danger' }}">
                                        {{ $venta->ticket->numero_ticket }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $venta->estado === 'Confirmada' ? 'success' : 'danger' }}">
                                    {{ $venta->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center py-3">Sin compras registradas.</p>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection