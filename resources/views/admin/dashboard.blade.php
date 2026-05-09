@extends('layouts.app')

@section('titulo', 'Panel Administrador')

@section('menu')
@include('admin.partials.menu')
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
                    <i class="bi bi-shield-check me-1"></i>{{ Auth::user()->rol->nombre }}
                </span>
                <span style="font-size:0.85rem">{{ now()->isoFormat('dddd, D [de] MMMM [de] Y') }}</span>
            </p>
        </div>
        <i class="bi bi-speedometer2 d-none d-md-block" style="font-size:3.5rem;opacity:0.25;color:#fff"></i>
    </div>
</div>

{{-- Módulos --}}
@php
$modulos = [
    ['permiso'=>'usuarios',    'ruta'=>'admin.usuarios.index',    'icono'=>'bi-people-fill',         'label'=>'Usuarios',     'desc'=>'Cuentas del sistema',    'bg'=>'linear-gradient(135deg,#1a5276,#2980b9)'],
    ['permiso'=>'clientes',    'ruta'=>'admin.clientes.index',    'icono'=>'bi-person-lines-fill',   'label'=>'Clientes',     'desc'=>'Base de pasajeros',      'bg'=>'linear-gradient(135deg,#00695C,#26A69A)'],
    ['permiso'=>'ventas',      'ruta'=>'admin.ventas.index',      'icono'=>'bi-cart-check-fill',     'label'=>'Ventas',       'desc'=>'Historial de compras',   'bg'=>'linear-gradient(135deg,#1B5E20,#43A047)'],
    ['permiso'=>'devoluciones','ruta'=>'admin.devoluciones.index','icono'=>'bi-arrow-return-left',   'label'=>'Devoluciones', 'desc'=>'Gestión de reembolsos',  'bg'=>'linear-gradient(135deg,#B71C1C,#E53935)'],
    ['permiso'=>'ingresos',    'ruta'=>'admin.ingresos.index',    'icono'=>'bi-graph-up-arrow',      'label'=>'Ingresos',     'desc'=>'Flujo de entrada',       'bg'=>'linear-gradient(135deg,#004D40,#00897B)'],
    ['permiso'=>'egresos',     'ruta'=>'admin.egresos.index',     'icono'=>'bi-graph-down-arrow',    'label'=>'Egresos',      'desc'=>'Flujo de salida',        'bg'=>'linear-gradient(135deg,#E65100,#FF7043)'],
    ['permiso'=>'reportes',    'ruta'=>'admin.reportes.index',    'icono'=>'bi-file-earmark-bar-graph','label'=>'Reportes',   'desc'=>'Informes y estadísticas','bg'=>'linear-gradient(135deg,#1A237E,#3949AB)'],
    ['permiso'=>'roles',       'ruta'=>'admin.roles.index',       'icono'=>'bi-shield-lock-fill',    'label'=>'Roles',        'desc'=>'Gestión de accesos',     'bg'=>'linear-gradient(135deg,#4A148C,#7B1FA2)'],
    ['permiso'=>'permisos',    'ruta'=>'admin.permisos.index',    'icono'=>'bi-key-fill',            'label'=>'Permisos',     'desc'=>'Control por módulo',     'bg'=>'linear-gradient(135deg,#37474F,#546E7A)'],
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
