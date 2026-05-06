@extends('layouts.app')

@section('titulo', 'Detalle Programación')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-calendar3"></i> Detalle de Programación: {{ $programacion->vuelo->codigo_vuelo }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Vuelo:</strong>
                        <p class="text-primary fs-5">{{ $programacion->vuelo->codigo_vuelo }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Ruta:</strong>
                        <p class="fs-5">
                            {{ $programacion->ruta->aeropuertoOrigen->codigo_IATA }}
                            <i class="bi bi-arrow-right"></i>
                            {{ $programacion->ruta->aeropuertoDestino->codigo_IATA }}
                        </p>
                        <small class="text-muted">{{ $programacion->ruta->aeropuertoOrigen->ciudad }} → {{ $programacion->ruta->aeropuertoDestino->ciudad }}</small>
                    </div>
                    <div class="col-md-3">
                        <strong>Aeronave:</strong>
                        <p>{{ $programacion->aeronave->matricula }}<br>
                        <small class="text-muted">{{ $programacion->aeronave->modelo }}</small></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Estado:</strong>
                        <p>
                            @if($programacion->estado === 'Programado')
                                <span class="badge bg-primary fs-6">Programado</span>
                            @elseif($programacion->estado === 'Completo')
                                <span class="badge bg-warning text-dark fs-6">Completo</span>
                            @else
                                <span class="badge bg-success fs-6">Salido</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Fecha Salida:</strong>
                        <p>{{ $programacion->fecha_salida }} - {{ $programacion->hora_salida }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Llegada:</strong>
                        <p>{{ $programacion->fecha_llegada }} - {{ $programacion->hora_llegada }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Precio Base:</strong>
                        <p class="fs-5 text-success">${{ number_format($programacion->precio_base, 2) }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Asientos Vendidos:</strong>
                        <p class="fs-5">{{ $programacion->asientos_vendidos }} / {{ $programacion->aeronave->capacidad_total }}</p>
                    </div>
                </div>

                {{-- Itinerario de escalas para vuelo padre --}}
                @if($programacion->vuelo->tipo === 'ConEscalas' && !$programacion->vuelo->vuelo_padre_id && $tramosEscala->count() > 0)
                <hr>
                <h5><i class="bi bi-diagram-3"></i> Itinerario de Escalas</h5>
                <p class="text-muted">Este vuelo realiza las siguientes paradas intermedias:</p>

                <div class="card border-primary mb-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">Tramo</th>
                                        <th>Código</th>
                                        <th>Origen</th>
                                        <th></th>
                                        <th>Destino</th>
                                        <th>Salida</th>
                                        <th>Llegada</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tramosEscala as $index => $tramo)
                                    <tr>
                                        <td class="text-center"><span class="badge bg-primary rounded-circle">{{ $index + 1 }}</span></td>
                                        <td><strong>{{ $tramo->vuelo->codigo_vuelo }}</strong></td>
                                        <td>
                                            <strong>{{ $tramo->ruta->aeropuertoOrigen->codigo_IATA }}</strong>
                                            <br><small class="text-muted">{{ $tramo->ruta->aeropuertoOrigen->ciudad }}</small>
                                        </td>
                                        <td class="text-center"><i class="bi bi-arrow-right text-primary"></i></td>
                                        <td>
                                            <strong>{{ $tramo->ruta->aeropuertoDestino->codigo_IATA }}</strong>
                                            <br><small class="text-muted">{{ $tramo->ruta->aeropuertoDestino->ciudad }}</small>
                                        </td>
                                        <td>{{ $tramo->fecha_salida }}<br><small>{{ $tramo->hora_salida }}</small></td>
                                        <td>{{ $tramo->fecha_llegada }}<br><small>{{ $tramo->hora_llegada }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $tramo->estado === 'Programado' ? 'primary' : ($tramo->estado === 'Completo' ? 'warning' : 'success') }}">
                                                {{ $tramo->estado }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Mostrar tiempo de escala entre tramos --}}
                                    @if(!$loop->last)
                                        @php
                                            $llegadaActual = \Carbon\Carbon::parse($tramo->fecha_llegada . ' ' . $tramo->hora_llegada);
                                            $salidaSiguiente = \Carbon\Carbon::parse($tramosEscala[$index + 1]->fecha_salida . ' ' . $tramosEscala[$index + 1]->hora_salida);
                                            $tiempoEscala = $llegadaActual->diff($salidaSiguiente);
                                        @endphp
                                        <tr class="table-warning">
                                            <td colspan="8" class="text-center">
                                                <i class="bi bi-clock"></i>
                                                <strong>Escala en {{ $tramo->ruta->aeropuertoDestino->ciudad }}:</strong>
                                                {{ $tiempoEscala->h }} hora(s) {{ $tiempoEscala->i }} minuto(s)
                                            </td>
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Línea de tiempo visual --}}
                <div class="d-flex align-items-center justify-content-center mb-3">
                    @foreach($tramosEscala as $index => $tramo)
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <strong>{{ $tramo->ruta->aeropuertoOrigen->codigo_IATA }}</strong>
                            </div>
                            <br><small>{{ $tramo->hora_salida }}</small>
                        </div>
                        <div class="flex-grow-1 mx-2">
                            <hr class="border-primary border-2">
                            <small class="text-muted d-block text-center" style="margin-top: -20px;">
                                <i class="bi bi-airplane"></i> {{ $tramo->vuelo->codigo_vuelo }}
                            </small>
                        </div>
                        @if($loop->last)
                        <div class="text-center">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <strong>{{ $tramo->ruta->aeropuertoDestino->codigo_IATA }}</strong>
                            </div>
                            <br><small>{{ $tramo->hora_llegada }}</small>
                        </div>
                        @endif
                    @endforeach
                </div>
                @elseif($programacion->vuelo->tipo === 'ConEscalas' && !$programacion->vuelo->vuelo_padre_id && $tramosEscala->count() === 0)
                <hr>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Este vuelo es de tipo "Con Escalas" pero aún no tiene tramos programados.
                </div>
                @endif

                {{-- Información de escala para tramo hijo --}}
                @if($programacion->vuelo->vuelo_padre_id && $tramosEscala->count() > 0)
                <hr>
                <div class="alert alert-warning">
                    <h5><i class="bi bi-diagram-3"></i> Este vuelo es un tramo de escala</h5>
                    <p class="mb-2">Pertenece al vuelo principal: <strong class="fs-5">{{ $programacion->vuelo->vueloPadre->codigo_vuelo }}</strong></p>

                    <h6>Itinerario completo del viaje:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered bg-white mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Orden</th>
                                    <th>Tramo</th>
                                    <th>Ruta</th>
                                    <th>Salida</th>
                                    <th>Llegada</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tramosEscala as $index => $tramo)
                                <tr class="{{ $tramo->id === $programacion->id ? 'table-active' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $tramo->vuelo->codigo_vuelo }}</strong>
                                        @if($tramo->id === $programacion->id)
                                            <span class="badge bg-primary">Actual</span>
                                        @endif
                                    </td>
                                    <td>{{ $tramo->ruta->aeropuertoOrigen->codigo_IATA }} <i class="bi bi-arrow-right"></i> {{ $tramo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                                    <td>{{ $tramo->fecha_salida }} {{ $tramo->hora_salida }}</td>
                                    <td>{{ $tramo->fecha_llegada }} {{ $tramo->hora_llegada }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tramo->estado === 'Programado' ? 'primary' : ($tramo->estado === 'Completo' ? 'warning' : 'success') }}">
                                            {{ $tramo->estado }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Tripulación --}}
                @if($programacion->tripulacion->count() > 0)
                <hr>
                <h5><i class="bi bi-people"></i> Tripulación Asignada</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Cargo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programacion->tripulacion as $trip)
                        <tr>
                            <td>{{ $trip->empleado->nombre }} {{ $trip->empleado->apellido }}</td>
                            <td><span class="badge bg-secondary">{{ $trip->cargo }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-exclamation-triangle"></i> Sin tripulación asignada.
                </div>
                @endif

                <hr>
                <a href="{{ route('operador.programaciones.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection