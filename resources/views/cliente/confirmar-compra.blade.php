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
                        <td>{{ $programacion->codigo_vuelo }}</td>
                    </tr>
                    <tr>
                        <th>Ruta</th>
                        <td>
                            @if(isset($subTramo) && $subTramo)
                                {{ $subTramo->aeropuertoOrigen->codigo_IATA }} → {{ $subTramo->aeropuertoDestino->codigo_IATA }}
                                <small class="d-block text-muted">Tramo parcial · vuelo {{ $programacion->codigo_vuelo }}</small>
                            @else
                                {{ $programacion->aeropuertoOrigen->codigo_IATA }} → {{ $programacion->aeropuertoDestino->codigo_IATA }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td>{{ $programacion->fecha_salida }} {{ $programacion->hora_salida }}</td>
                    </tr>
                    <tr>
                        <th>Asiento</th>
                        <td>{{ $asiento->numero }} ({{ $asiento->tipoClase->nombre }})</td>
                    </tr>
                    @php
                        $precioProg = $programacion->precios->firstWhere('tipo_clase_id', $asiento->tipo_clase_id);
                        $precioAsiento = $precioProg ? $precioProg->precio : ($programacion->precio_base * $asiento->tipoClase->multiplicador_precio);
                    @endphp
                    <tr class="table-success">
                        <th>Total a Pagar</th>
                        <td class="fs-5 fw-bold">Bs. {{ number_format($precioAsiento, 2) }}</td>
                    </tr>
                </table>

                <form method="POST" action="{{ route('cliente.procesar.compra') }}">
                    @csrf
                    <input type="hidden" name="programacion_vuelo_id" value="{{ $programacion->id }}">
                    <input type="hidden" name="asiento_id" value="{{ $asiento->id }}">
                    @if(isset($subTramo) && $subTramo)
                    <input type="hidden" name="sub_tramo_id" value="{{ $subTramo->id }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-person-fill me-1"></i>Datos del Pasajero</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('pasajero_nombre') is-invalid @enderror"
                                       name="pasajero_nombre"
                                       value="{{ old('pasajero_nombre', $cliente->nombre) }}"
                                       placeholder="Nombre" required>
                                @error('pasajero_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('pasajero_apellido') is-invalid @enderror"
                                       name="pasajero_apellido"
                                       value="{{ old('pasajero_apellido', $cliente->apellido) }}"
                                       placeholder="Apellido" required>
                                @error('pasajero_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

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