@extends('layouts.app')

@section('titulo', 'Panel Operador')

@section('menu')
@include('operador.partials.menu')
@endsection

@section('contenido')

{{-- Bienvenida --}}
<div class="dash-welcome">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1">
                Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,0.8)">
                <span class="badge me-2" style="background:rgba(255,255,255,0.2)">
                    <i class="bi bi-person-gear me-1"></i>{{ Auth::user()->rol->nombre }}
                </span>
                <span style="font-size:0.85rem">{{ now()->isoFormat('dddd, D [de] MMMM [de] Y') }}</span>
            </p>
        </div>
        <i class="bi bi-airplane-fill d-none d-md-block" style="font-size:3.5rem;opacity:0.25;color:#fff"></i>
    </div>
</div>

{{-- Módulos --}}
@php
$modulos = [
    ['permiso'=>'aeropuertos',       'ruta'=>'operador.aeropuertos.index',   'icono'=>'bi-geo-alt-fill',        'label'=>'Aeropuertos',   'desc'=>'Gestión de terminales',    'bg'=>'linear-gradient(135deg,#1a5276,#2980b9)'],
    ['permiso'=>'aeronaves',         'ruta'=>'operador.aeronaves.index',     'icono'=>'bi-airplane-engines',    'label'=>'Aeronaves',     'desc'=>'Flota aérea',              'bg'=>'linear-gradient(135deg,#004D40,#00897B)'],
    ['permiso'=>'tipo_clases',       'ruta'=>'operador.tipo-clases.index',   'icono'=>'bi-star-fill',           'label'=>'Tipo Clases',   'desc'=>'Categorías de asiento',    'bg'=>'linear-gradient(135deg,#4A148C,#7B1FA2)'],
    ['permiso'=>'asientos',          'ruta'=>'operador.asientos.index',      'icono'=>'bi-grid-3x3-gap-fill',   'label'=>'Asientos',      'desc'=>'Distribución de cabina',   'bg'=>'linear-gradient(135deg,#37474F,#546E7A)'],
    ['permiso'=>'rutas',             'ruta'=>'operador.rutas.index',         'icono'=>'bi-signpost-2-fill',     'label'=>'Rutas',         'desc'=>'Conexiones nacionales',    'bg'=>'linear-gradient(135deg,#E65100,#FF7043)'],
    ['permiso'=>'programacion_vuelos','ruta'=>'operador.programaciones.index','icono'=>'bi-calendar3',          'label'=>'Programación',  'desc'=>'Horarios y fechas',        'bg'=>'linear-gradient(135deg,#1A237E,#3949AB)'],
    ['permiso'=>'empleados',         'ruta'=>'operador.empleados.index',     'icono'=>'bi-person-badge-fill',   'label'=>'Empleados',     'desc'=>'Personal operativo',       'bg'=>'linear-gradient(135deg,#B71C1C,#E53935)'],
    ['permiso'=>'tripulaciones',     'ruta'=>'operador.tripulaciones.index', 'icono'=>'bi-people-fill',         'label'=>'Tripulación',   'desc'=>'Asignación de crew',       'bg'=>'linear-gradient(135deg,#00695C,#26A69A)'],
    ['permiso'=>'salidas',           'ruta'=>'operador.salidas.index',       'icono'=>'bi-box-arrow-right',     'label'=>'Salidas',       'desc'=>'Control de abordaje',      'bg'=>'linear-gradient(135deg,#F57F17,#FFA000)'],
];
@endphp

<div class="row g-3">
    @foreach($modulos as $m)
    @if($permisosUsuario->contains($m['permiso']))
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card module-card shadow-sm h-100">
            <div class="module-card-icon" style="background: {{ $m['bg'] }}">
                <i class="bi {{ $m['icono'] }}"></i>
            </div>
            <div class="card-body text-center">
                <h6>{{ $m['label'] }}</h6>
                <p class="card-text">{{ $m['desc'] }}</p>
                <a href="{{ route($m['ruta']) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>Gestionar
                </a>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

@endsection
