@extends('layouts.app')

@section('titulo', 'Mis Reservas')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-bookmark"></i> Mis Reservas</h2>

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
                            <th>Estado</th>
                            <th>Fecha Reserva</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservas as $reserva)
                        <tr>
                            <td><strong>{{ $reserva->codigo_reserva }}</strong></td>
                            <td>{{ $reserva->programacionVuelo->vuelo->codigo_vuelo }}</td>
                            <td>{{ $reserva->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $reserva->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                            <td>{{ $reserva->programacionVuelo->fecha_salida }} {{ $reserva->programacionVuelo->hora_salida }}</td>
                            <td>{{ $reserva->asiento->numero }} ({{ $reserva->asiento->tipoClase->nombre }})</td>
                            <td>${{ number_format($reserva->monto, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $reserva->estado === 'Confirmada' ? 'success' : 'danger' }}">
                                    {{ $reserva->estado }}
                                </span>
                            </td>
                            <td>{{ $reserva->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No tiene reservas registradas.
                                <br><a href="{{ route('cliente.buscar') }}" class="btn btn-primary btn-sm mt-2">Buscar Vuelos</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $reservas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection