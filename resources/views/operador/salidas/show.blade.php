@extends('layouts.app')

@section('titulo', 'Detalle de Salida')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-box-arrow-right"></i> Detalle de Salida: {{ $salida->programacionVuelo->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Vuelo:</strong>
                        <p class="text-primary fs-5">{{ $salida->programacionVuelo->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Ruta:</strong>
                        <p>{{ $salida->programacionVuelo->aeropuertoOrigen->codigo_IATA }} → {{ $salida->programacionVuelo->aeropuertoDestino->codigo_IATA }}
                        <br><small class="text-muted">{{ $salida->programacionVuelo->aeropuertoOrigen->ciudad }} → {{ $salida->programacionVuelo->aeropuertoDestino->ciudad }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Aeronave:</strong>
                        <p>{{ $salida->programacionVuelo->aeronave->matricula }}
                        <br><small class="text-muted">{{ $salida->programacionVuelo->aeronave->modelo }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Salida:</strong>
                        <p>{{ $salida->programacionVuelo->fecha_salida }}
                        <br>{{ $salida->programacionVuelo->hora_salida }}</p>
                    </div>
                </div>

                {{-- Resumen financiero --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>Monto Total Recaudado</h6>
                                <h3>${{ number_format($salida->monto_total_recaudado, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h6>Pasajes Vendidos</h6>
                                <h3>{{ $ventas->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>Ingreso Financiero</h6>
                                <h3>
                                    @if($salida->ingreso)
                                        <i class="bi bi-check-circle"></i> Registrado
                                    @else
                                        <i class="bi bi-clock"></i> Pendiente
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detalle de ventas --}}
                <h5><i class="bi bi-receipt"></i> Detalle de Ventas</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código Venta</th>
                                <th>Pasajero</th>
                                <th>Asiento</th>
                                <th>Clase</th>
                                <th>Monto</th>
                                <th>Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventas as $venta)
                            <tr>
                                <td><strong>{{ $venta->codigo_venta }}</strong></td>
                                <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</td>
                                <td>{{ $venta->tickets->first()?->asiento?->numero ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $venta->tickets->first()?->asiento?->tipoClase?->nombre ?? '-' }}</span></td>
                                <td>${{ number_format($venta->monto_total, 2) }}</td>
                                <td>
                                    @if($venta->tickets->isNotEmpty())
                                        <span class="badge bg-success">{{ $venta->tickets->first()->numero_ticket }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-success">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                                <td class="fw-bold">${{ number_format($ventas->sum('monto_total'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <a href="{{ route('operador.salidas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection