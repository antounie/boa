@extends('layouts.app')

@section('titulo', 'Mis Compras')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-cart-check"></i> Mis Compras</h2>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Monto</th>
                            <th>Pago</th>
                            <th>Ticket</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td><strong>{{ $venta->codigo_venta }}</strong></td>
                            <td>{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $venta->programacionVuelo->fecha_salida }} {{ $venta->programacionVuelo->hora_salida }}</td>
                            <td>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</td>
                            <td>${{ number_format($venta->monto_total, 2) }}</td>
                            <td><span class="badge bg-info">{{ $venta->metodo_pago }}</span></td>
                            <td>
                                @if($venta->ticket)
                                    <span class="badge bg-{{ $venta->ticket->estado === 'Emitido' ? 'primary' : 'danger' }}">
                                        {{ $venta->ticket->numero_ticket }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $venta->estado === 'Confirmada' ? 'success' : 'danger' }}">
                                    {{ $venta->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No tiene compras registradas.
                                <br><a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm mt-2">Buscar Vuelos</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection