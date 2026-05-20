@extends('layouts.app')

@section('titulo', 'Pago por QR')

@section('menu')
@include('cliente.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h5 class="mb-0"><i class="bi bi-qr-code"></i> Pago por Código QR</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Detalles del vuelo --}}
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Detalles de la Compra</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Vuelo</th>
                                <td>{{ $programacion->codigo_vuelo }}</td>
                            </tr>
                            <tr>
                                <th>Ruta</th>
                                <td>
                                    @if($subTramo)
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
                                <th>Asientos / Pasajeros</th>
                                <td>
                                    @foreach($asientos as $asiento)
                                    @php
                                        $precioProg = $programacion->precios->firstWhere('tipo_clase_id', $asiento->tipo_clase_id);
                                        $precioAsiento = $precioProg ? $precioProg->precio : ($programacion->precio_base * $asiento->tipoClase->multiplicador_precio);
                                        $pax = $pasajeros[$asiento->id] ?? null;
                                    @endphp
                                    <div class="border-bottom pb-1 mb-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-semibold">{{ $asiento->numero }} <small class="text-muted">({{ $asiento->tipoClase->nombre }})</small></span>
                                            <span class="text-primary">Bs. {{ number_format($precioAsiento, 2) }}</span>
                                        </div>
                                        @if($pax)
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $pax['nombre'] }} {{ $pax['apellido'] }}</small>
                                        @endif
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Comprador</th>
                                <td>{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                            </tr>
                            <tr class="table-success">
                                <th>Total a Pagar</th>
                                <td class="fs-4 fw-bold text-success">Bs. {{ number_format($monto, 2) }}</td>
                            </tr>
                        </table>

                        <div class="alert alert-info">
                            <small><i class="bi bi-info-circle"></i> Referencia: <strong>{{ $identificador }}</strong></small>
                        </div>
                    </div>

                    {{-- Código QR --}}
                    <div class="col-md-6 text-center">
                        <h6 class="text-muted mb-3">Escanea el código QR para pagar</h6>

                        @if($resultado['modo'] === 'libelula' && $resultado['qr_url'])
                            {{-- QR Real de Libélula --}}
                            <div class="border rounded p-3 mb-3 bg-white d-inline-block">
                                <img src="{{ $resultado['qr_url'] }}" alt="QR de Pago" style="width: 250px; height: 250px;">
                            </div>

                            <p class="text-success fw-bold mb-1">
                                <i class="bi bi-shield-check"></i> QR Simple - Pago Real
                            </p>
                            <p class="text-muted small">
                                Escanea con tu app bancaria (Banco Unión, BNB, Mercantil, etc.)
                            </p>

                            <div class="alert alert-success mt-3">
                                <small><i class="bi bi-check-circle"></i> Pasarela de pago: <strong>Libélula</strong></small>
                            </div>

                            {{-- Botón para ir a la pasarela completa --}}
                            @if($resultado['url_pasarela'])
                            <a href="{{ $resultado['url_pasarela'] }}" class="btn btn-primary mb-2" target="_blank">
                                <i class="bi bi-credit-card"></i> Pagar en Pasarela Libélula
                            </a>
                            @endif

                            <div id="estadoPago" class="mb-3 mt-3">
                                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                                <span class="text-muted">Esperando confirmación de pago...</span>
                            </div>

                            {{-- Botón para confirmar manualmente (testing) --}}
                            <form id="formConfirmarPago" action="{{ route('cliente.pago.confirmar-simulacion') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-check-circle"></i> He realizado el pago
                                </button>
                            </form>

                        @else
                            {{-- Modo Simulación --}}
                            <div class="border rounded p-4 mb-3 bg-white d-inline-block">
                                <div class="bg-light p-4 rounded">
                                    <i class="bi bi-qr-code" style="font-size: 150px; color: #333;"></i>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <small><i class="bi bi-exclamation-triangle"></i> <strong>Modo Simulación:</strong> Libélula no disponible</small>
                            </div>

                            <form action="{{ route('cliente.pago.confirmar-simulacion') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Confirmar Pago (Simulación)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('cliente.buscar') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let verificando = false;
    
    setInterval(function() {
        if (verificando) return;
        verificando = true;

        const estadoPago = document.getElementById('estadoPago');
        if (estadoPago) {
            estadoPago.innerHTML = '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> <span class="text-muted">Verificando pago con Libélula...</span>';
        }
        
        fetch('{{ route("cliente.pago.resultado", $identificador) }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            
            if (data.status === 'completado') {
                if (estadoPago) {
                    estadoPago.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Pago confirmado! Procesando compra...</span>';
                }
                // Enviar formulario para completar la compra
                document.getElementById('formConfirmarPago').submit();
            } else {
                if (estadoPago) {
                    estadoPago.innerHTML = '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> <span class="text-muted">Esperando confirmación de pago...</span>';
                }
            }
            verificando = false;
        })
        .catch(() => {
            if (estadoPago) {
                estadoPago.innerHTML = '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> <span class="text-muted">Esperando confirmación de pago...</span>';
            }
            verificando = false;
        });
    }, 8000);
</script>
@endpush