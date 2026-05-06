@extends('layouts.app')

@section('titulo', 'Confirmar Compra')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Confirmar Compra Directa</h5>
            </div>
            <div class="card-body p-4">
                <h6 class="text-muted">Detalles del Vuelo</h6>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th>Vuelo</th>
                        <td>{{ $programacion->vuelo->codigo_vuelo }}</td>
                    </tr>
                    <tr>
                        <th>Ruta</th>
                        <td>{{ $programacion->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $programacion->ruta->aeropuertoDestino->codigo_IATA }}</td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td>{{ $programacion->fecha_salida }} {{ $programacion->hora_salida }}</td>
                    </tr>
                    <tr>
                        <th>Asiento</th>
                        <td>{{ $asiento->numero }} ({{ $asiento->tipoClase->nombre }})</td>
                    </tr>
                    <tr>
                        <th>Pasajero</th>
                        <td>{{ $cliente->nombre }} {{ $cliente->apellido }} ({{ $cliente->documento_identidad }})</td>
                    </tr>
                    <tr class="table-success">
                        <th>Total a Pagar</th>
                        <td class="fs-5 fw-bold">${{ number_format($programacion->precio_base * $asiento->tipoClase->multiplicador_precio, 2) }}</td>
                    </tr>
                </table>

                <form method="POST" action="{{ route('cliente.procesar.compra') }}">
                    @csrf
                    <input type="hidden" name="programacion_vuelo_id" value="{{ $programacion->id }}">
                    <input type="hidden" name="asiento_id" value="{{ $asiento->id }}">

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-credit-card"></i> Método de Pago</label>
                        <select class="form-select" name="metodo_pago" required>
                            <option value="">Seleccionar método...</option>
                            <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="QR">Pago por QR</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cliente.buscar') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Confirmar y Pagar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection