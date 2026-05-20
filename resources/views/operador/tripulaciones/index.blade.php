@extends('layouts.app')

@section('titulo', 'Gestionar Tripulación')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')
<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-people-fill"></i> Gestionar Tripulación</h2>
        <p class="text-muted">Seleccione una programación de vuelo para asignar tripulación.</p>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operador.tripulaciones.index') }}" class="row g-3">
                    <div class="col-md-9">
                        <select class="form-select" name="programacion_id" onchange="this.form.submit()">
                            <option value="">Seleccionar programación de vuelo...</option>
                            @foreach($programaciones as $prog)
                                <option value="{{ $prog->id }}" {{ request('programacion_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->codigo_vuelo }} | {{ $prog->aeropuertoOrigen->codigo_IATA }} → {{ $prog->aeropuertoDestino->codigo_IATA }} | {{ $prog->fecha_salida }} {{ $prog->hora_salida }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary w-100">Ver Tripulación</button>
                    </div>
                </form>
            </div>
        </div>

        @if($programacionSeleccionada)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-airplane"></i>
                            {{ $programacionSeleccionada->codigo_vuelo }} |
                            {{ $programacionSeleccionada->aeropuertoOrigen->codigo_IATA }} → {{ $programacionSeleccionada->aeropuertoDestino->codigo_IATA }} |
                            {{ $programacionSeleccionada->fecha_salida }} {{ $programacionSeleccionada->hora_salida }}
                        </h5>
                        <small>Aeronave: {{ $programacionSeleccionada->aeronave->matricula }} - {{ $programacionSeleccionada->aeronave->modelo }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    {{-- Resumen de tripulación --}}
                    @php
                        $pilotos = $tripulacion->where('cargo', 'Piloto')->count();
                        $copilotos = $tripulacion->where('cargo', 'Copiloto')->count();
                        $auxiliares = $tripulacion->where('cargo', 'Auxiliar')->count();
                    @endphp
                    <div class="col-md-4">
                        <div class="card border-primary text-center">
                            <div class="card-body">
                                <h3 class="text-primary">{{ $pilotos }}</h3>
                                <p class="mb-0">Piloto(s)</p>
                                <a href="{{ route('operador.tripulaciones.create', ['programacion_id' => $programacionSeleccionada->id, 'cargo' => 'Piloto']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-plus"></i> Asignar Piloto
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info text-center">
                            <div class="card-body">
                                <h3 class="text-info">{{ $copilotos }}</h3>
                                <p class="mb-0">Copiloto(s)</p>
                                <a href="{{ route('operador.tripulaciones.create', ['programacion_id' => $programacionSeleccionada->id, 'cargo' => 'Copiloto']) }}" class="btn btn-sm btn-outline-info mt-2">
                                    <i class="bi bi-plus"></i> Asignar Copiloto
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary text-center">
                            <div class="card-body">
                                <h3 class="text-secondary">{{ $auxiliares }}</h3>
                                <p class="mb-0">Auxiliar(es)</p>
                                <a href="{{ route('operador.tripulaciones.create', ['programacion_id' => $programacionSeleccionada->id, 'cargo' => 'Auxiliar']) }}" class="btn btn-sm btn-outline-secondary mt-2">
                                    <i class="bi bi-plus"></i> Asignar Auxiliar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($tripulacion->count() > 0)
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre Completo</th>
                            <th>Cargo</th>
                            <th>Licencia</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tripulacion as $trip)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $trip->empleado->nombre }} {{ $trip->empleado->apellido }}</strong></td>
                            <td>
                                @if($trip->cargo === 'Piloto')
                                    <span class="badge bg-primary">Piloto</span>
                                @elseif($trip->cargo === 'Copiloto')
                                    <span class="badge bg-info">Copiloto</span>
                                @else
                                    <span class="badge bg-secondary">Auxiliar</span>
                                @endif
                            </td>
                            <td>{{ $trip->empleado->licencia ?? '-' }}</td>
                            <td class="text-center">
                                <form action="{{ route('operador.tripulaciones.destroy', $trip) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Remover"
                                            onclick="return confirm('¿Está seguro de remover a {{ $trip->empleado->nombre }} {{ $trip->empleado->apellido }} de este vuelo?')">
                                        <i class="bi bi-person-x"></i> Remover
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Este vuelo no tiene tripulación asignada. Use los botones de arriba para asignar.
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection