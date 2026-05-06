@extends('layouts.app')

@section('titulo', 'Buscar Información')

@section('menu')
@auth
    @if(Auth::user()->rol_id === 1)
        @include('admin.partials.menu')
    @elseif(Auth::user()->rol_id === 2)
        @include('operador.partials.menu')
    @elseif(Auth::user()->rol_id === 3)
        @include('cliente.partials.menu')
    @endif
@endauth
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-search"></i> Resultados de Búsqueda</h2>

        @if($q)
            <p class="text-muted">Se encontraron <strong>{{ $totalResultados }}</strong> resultado(s) para: "<strong>{{ $q }}</strong>"</p>
        @else
            <p class="text-muted">Ingrese un término de búsqueda en la barra superior.</p>
        @endif

        @if($totalResultados > 0)

            {{-- Aeropuertos --}}
            @if($resultados['aeropuertos']->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Aeropuertos ({{ $resultados['aeropuertos']->count() }})</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código IATA</th>
                                <th>Nombre</th>
                                <th>Ciudad</th>
                                <th>País</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados['aeropuertos'] as $aeropuerto)
                            <tr>
                                <td><strong class="text-primary">{{ $aeropuerto->codigo_IATA }}</strong></td>
                                <td>{{ $aeropuerto->nombre }}</td>
                                <td>{{ $aeropuerto->ciudad }}</td>
                                <td>{{ $aeropuerto->pais }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Rutas --}}
            @if($resultados['rutas']->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-signpost-2"></i> Rutas ({{ $resultados['rutas']->count() }})</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Origen</th>
                                <th></th>
                                <th>Destino</th>
                                <th>Distancia</th>
                                <th>Duración</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados['rutas'] as $ruta)
                            <tr>
                                <td>
                                    <strong>{{ $ruta->aeropuertoOrigen->codigo_IATA }}</strong>
                                    <br><small class="text-muted">{{ $ruta->aeropuertoOrigen->ciudad }}</small>
                                </td>
                                <td class="text-center"><i class="bi bi-arrow-right text-primary"></i></td>
                                <td>
                                    <strong>{{ $ruta->aeropuertoDestino->codigo_IATA }}</strong>
                                    <br><small class="text-muted">{{ $ruta->aeropuertoDestino->ciudad }}</small>
                                </td>
                                <td>{{ number_format($ruta->distancia, 0) }} km</td>
                                <td>{{ $ruta->duracion_estimada }}</td>
                                <td>
                                    <span class="badge bg-{{ $ruta->tipo === 'Nacional' ? 'success' : 'info' }}">{{ $ruta->tipo }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Vuelos --}}
            @if($resultados['vuelos']->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-airplane"></i> Vuelos ({{ $resultados['vuelos']->count() }})</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados['vuelos'] as $vuelo)
                            <tr>
                                <td><strong class="text-primary">{{ $vuelo->codigo_vuelo }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $vuelo->tipo === 'Directo' ? 'primary' : 'warning' }}">
                                        {{ $vuelo->tipo === 'ConEscalas' ? 'Con Escalas' : $vuelo->tipo }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $vuelo->estado === 'Activo' ? 'success' : 'danger' }}">{{ $vuelo->estado }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Programaciones disponibles --}}
            @if($resultados['programaciones']->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Vuelos Disponibles ({{ $resultados['programaciones']->count() }})</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Vuelo</th>
                                <th>Ruta</th>
                                <th>Aeronave</th>
                                <th>Fecha Salida</th>
                                <th>Hora</th>
                                <th>Precio Base</th>
                                <th>Disponibilidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados['programaciones'] as $prog)
                            <tr>
                                <td><strong class="text-primary">{{ $prog->vuelo->codigo_vuelo }}</strong></td>
                                <td>
                                    {{ $prog->ruta->aeropuertoOrigen->codigo_IATA }}
                                    <i class="bi bi-arrow-right"></i>
                                    {{ $prog->ruta->aeropuertoDestino->codigo_IATA }}
                                    <br><small class="text-muted">{{ $prog->ruta->aeropuertoOrigen->ciudad }} → {{ $prog->ruta->aeropuertoDestino->ciudad }}</small>
                                </td>
                                <td>{{ $prog->aeronave->matricula }}</td>
                                <td>{{ $prog->fecha_salida }}</td>
                                <td>{{ $prog->hora_salida }}</td>
                                <td class="text-success fw-bold">${{ number_format($prog->precio_base, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ ($prog->aeronave->capacidad_total - $prog->asientos_vendidos) > 0 ? 'success' : 'danger' }}">
                                        {{ $prog->aeronave->capacidad_total - $prog->asientos_vendidos }} asientos
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        @elseif($q)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No se encontraron resultados para "<strong>{{ $q }}</strong>". Intente con otros términos como: ciudad, código IATA, código de vuelo.
            </div>
        @endif
    </div>
</div>
@endsection