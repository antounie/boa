<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('operador.dashboard') || request()->routeIs('cliente.dashboard') ? 'active' : '' }}"
       href="{{ (int) Auth::user()->rol_id === 1 ? route('admin.dashboard') : ((int) Auth::user()->rol_id === 2 ? route('operador.dashboard') : route('cliente.dashboard')) }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</li>

@if($permisosUsuario->contains('usuarios') || $permisosUsuario->contains('roles') || $permisosUsuario->contains('permisos'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.usuarios.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permisos.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-people-fill"></i> Usuarios y Acceso
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('usuarios'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('roles'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                <i class="bi bi-shield-lock"></i> Roles
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('permisos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}" href="{{ route('admin.permisos.index') }}">
                <i class="bi bi-key"></i> Permisos
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

@if($permisosUsuario->contains('aeropuertos') || $permisosUsuario->contains('tipo_clases') || $permisosUsuario->contains('aeronaves') || $permisosUsuario->contains('asientos') || $permisosUsuario->contains('rutas') || $permisosUsuario->contains('tramos') || $permisosUsuario->contains('programacion_vuelos'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('operador.aeropuertos.*') || request()->routeIs('operador.tipo-clases.*') || request()->routeIs('operador.aeronaves.*') || request()->routeIs('operador.asientos.*') || request()->routeIs('operador.rutas.*') || request()->routeIs('operador.tramos.*') || request()->routeIs('operador.programaciones.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-airplane"></i> Programación de Vuelos
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('aeropuertos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.aeropuertos.*') ? 'active' : '' }}" href="{{ route('operador.aeropuertos.index') }}">
                <i class="bi bi-geo-alt"></i> Aeropuertos
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('tipo_clases'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.tipo-clases.*') ? 'active' : '' }}" href="{{ route('operador.tipo-clases.index') }}">
                <i class="bi bi-star"></i> Tipo Clases
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('aeronaves'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.aeronaves.*') ? 'active' : '' }}" href="{{ route('operador.aeronaves.index') }}">
                <i class="bi bi-airplane-engines"></i> Aeronaves
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('asientos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.asientos.*') ? 'active' : '' }}" href="{{ route('operador.asientos.index') }}">
                <i class="bi bi-grid-3x3"></i> Asientos
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('rutas'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.rutas.*') ? 'active' : '' }}" href="{{ route('operador.rutas.index') }}">
                <i class="bi bi-signpost-2"></i> Rutas
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('tramos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.tramos.*') ? 'active' : '' }}" href="{{ route('operador.tramos.index') }}">
                <i class="bi bi-diagram-3"></i> Tramos
            </a>
        </li>
        @endif
@if($permisosUsuario->contains('programacion_vuelos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.programaciones.*') ? 'active' : '' }}" href="{{ route('operador.programaciones.index') }}">
                <i class="bi bi-calendar3"></i> Programación
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

@if($permisosUsuario->contains('empleados') || $permisosUsuario->contains('tripulaciones'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('operador.empleados.*') || request()->routeIs('operador.tripulaciones.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-badge"></i> Personal
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('empleados'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.empleados.*') ? 'active' : '' }}" href="{{ route('operador.empleados.index') }}">
                <i class="bi bi-person-badge"></i> Empleados
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('tripulaciones'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('operador.tripulaciones.*') ? 'active' : '' }}" href="{{ route('operador.tripulaciones.index') }}">
                <i class="bi bi-people-fill"></i> Tripulación
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

@if($permisosUsuario->contains('clientes') || $permisosUsuario->contains('ventas') || $permisosUsuario->contains('devoluciones'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.clientes.*') || request()->routeIs('admin.ventas.*') || request()->routeIs('admin.devoluciones.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-shop"></i> Comercial
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('clientes'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}" href="{{ route('admin.clientes.index') }}">
                <i class="bi bi-person-lines-fill"></i> Clientes
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('ventas'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.ventas.*') ? 'active' : '' }}" href="{{ route('admin.ventas.index') }}">
                <i class="bi bi-cart-check"></i> Ventas
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('devoluciones'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.devoluciones.*') ? 'active' : '' }}" href="{{ route('admin.devoluciones.index') }}">
                <i class="bi bi-arrow-return-left"></i> Devoluciones
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

@if($permisosUsuario->contains('salidas'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('operador.salidas.*') ? 'active' : '' }}" href="{{ route('operador.salidas.index') }}">
        <i class="bi bi-box-arrow-right"></i> Salidas
    </a>
</li>
@endif

@if($permisosUsuario->contains('ingresos') || $permisosUsuario->contains('egresos'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.ingresos.*') || request()->routeIs('admin.egresos.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-cash-stack"></i> Financiero
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('ingresos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.ingresos.*') ? 'active' : '' }}" href="{{ route('admin.ingresos.index') }}">
                <i class="bi bi-graph-up-arrow"></i> Ingresos
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('egresos'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('admin.egresos.*') ? 'active' : '' }}" href="{{ route('admin.egresos.index') }}">
                <i class="bi bi-graph-down-arrow"></i> Egresos
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

@if($permisosUsuario->contains('reportes'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" href="{{ route('admin.reportes.index') }}">
        <i class="bi bi-file-earmark-bar-graph"></i> Reportes
    </a>
</li>
@endif

@if($permisosUsuario->contains('reservas') || $permisosUsuario->contains('compras') || $permisosUsuario->contains('tickets'))
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('cliente.buscar*') || request()->routeIs('cliente.mis.*') ? 'active' : '' }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-ticket-perforated"></i> Pasajes
    </a>
    <ul class="dropdown-menu">
        @if($permisosUsuario->contains('reservas'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('cliente.buscar*') ? 'active' : '' }}" href="{{ route('cliente.buscar') }}">
                <i class="bi bi-search"></i> Buscar Vuelos
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('reservas'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('cliente.mis.reservas') ? 'active' : '' }}" href="{{ route('cliente.mis.reservas') }}">
                <i class="bi bi-bookmark"></i> Mis Reservas
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('compras'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('cliente.mis.compras') ? 'active' : '' }}" href="{{ route('cliente.mis.compras') }}">
                <i class="bi bi-cart-check"></i> Mis Compras
            </a>
        </li>
        @endif
        @if($permisosUsuario->contains('tickets'))
        <li>
            <a class="dropdown-item {{ request()->routeIs('cliente.mis.tickets') ? 'active' : '' }}" href="{{ route('cliente.mis.tickets') }}">
                <i class="bi bi-ticket-perforated"></i> Mis Tickets
            </a>
        </li>
        @endif
    </ul>
</li>
@endif
