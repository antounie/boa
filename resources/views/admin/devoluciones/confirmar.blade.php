@extends('layouts.app')

@section('titulo', 'Confirmar Devolución')

@section('menu')
@include('admin.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirmar Devolución</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning">
                    <strong>Atención:</strong> Esta acción cancelará la venta, anulará el ticket, liberará el asiento y generará un egreso financiero.
                </div>

                <h6 class="text-muted">Detalles de la Venta</h6>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th>Código Venta</th>
                        <td><strong>{{ $venta->codigo_venta }}</strong></td>
                    </tr>
                    <tr>
                        <th>Cliente</th>
                        <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }} ({{ $venta->cliente->documento_identidad }})</td>
                    </tr>
                    <tr>
                        <th>Vuelo</th>
                        <td>{{ $venta->programacionVuelo->vuelo->codigo_vuelo }}</td>
                    </tr>
                    <tr>
                        <th>Ruta</th>
                        <td>{{ $venta->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Vuelo</th>
                        <td>{{ $venta->programacionVuelo->fecha_salida }} {{ $venta->programacionVuelo->hora_salida }}</td>
                    </tr>
                    <tr>
                        <th>Asiento</th>
                        <td>{{ $venta->asiento->numero }} ({{ $venta->asiento->tipoClase->nombre }})</td>
                    </tr>
                    <tr>
                        <th>Ticket</th>
                        <td>
                            @if($venta->ticket)
                                {{ $venta->ticket->numero_ticket }} — <span class="badge bg-{{ $venta->ticket->estado === 'Emitido' ? 'success' : 'danger' }}">{{ $venta->ticket->estado }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr class="table-danger">
                        <th>Monto a Devolver</th>
                        <td class="fs-5 fw-bold text-danger">${{ number_format($venta->monto_total, 2) }}</td>
                    </tr>
                </table>

                <form method="POST" action="{{ route('admin.devoluciones.store') }}">
                    @csrf
                    <input type="hidden" name="venta_id" value="{{ $venta->id }}">

                    <div class="mb-3">
                        <label for="motivo" class="form-label"><i class="bi bi-chat-text"></i> Motivo de la Devolución</label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror"
                                  id="motivo" name="motivo" rows="3" required
                                  placeholder="Ingrese el motivo de la devolución...">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.devoluciones.create') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('¿Está seguro de procesar esta devolución? Esta acción no se puede deshacer.')">
                            <i class="bi bi-arrow-return-left"></i> Procesar Devolución
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection