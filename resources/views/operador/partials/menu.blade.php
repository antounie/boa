<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.dashboard') ? 'active' : '' }}" href="{{ route('operador.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

@if($permisosUsuario->contains('aeropuertos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.aeropuertos.*') ? 'active' : '' }}" href="{{ route('operador.aeropuertos.index') }}"><i class="bi bi-geo-alt"></i> Aeropuertos</a></li>
@endif

@if($permisosUsuario->contains('tipo_clases'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.tipo-clases.*') ? 'active' : '' }}" href="{{ route('operador.tipo-clases.index') }}"><i class="bi bi-star"></i> Tipo Clases</a></li>
@endif

@if($permisosUsuario->contains('aeronaves'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.aeronaves.*') ? 'active' : '' }}" href="{{ route('operador.aeronaves.index') }}"><i class="bi bi-airplane-engines"></i> Aeronaves</a></li>
@endif

@if($permisosUsuario->contains('asientos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.asientos.*') ? 'active' : '' }}" href="{{ route('operador.asientos.index') }}"><i class="bi bi-grid-3x3"></i> Asientos</a></li>
@endif

@if($permisosUsuario->contains('rutas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.rutas.*') ? 'active' : '' }}" href="{{ route('operador.rutas.index') }}"><i class="bi bi-signpost-2"></i> Rutas</a></li>
@endif

@if($permisosUsuario->contains('vuelos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.vuelos.*') ? 'active' : '' }}" href="{{ route('operador.vuelos.index') }}"><i class="bi bi-airplane"></i> Vuelos</a></li>
@endif

@if($permisosUsuario->contains('programacion_vuelos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.programaciones.*') ? 'active' : '' }}" href="{{ route('operador.programaciones.index') }}"><i class="bi bi-calendar3"></i> Programación</a></li>
@endif

@if($permisosUsuario->contains('empleados'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.empleados.*') ? 'active' : '' }}" href="{{ route('operador.empleados.index') }}"><i class="bi bi-person-badge"></i> Empleados</a></li>
@endif

@if($permisosUsuario->contains('tripulaciones'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.tripulaciones.*') ? 'active' : '' }}" href="{{ route('operador.tripulaciones.index') }}"><i class="bi bi-people-fill"></i> Tripulación</a></li>
@endif

@if($permisosUsuario->contains('salidas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('operador.salidas.*') ? 'active' : '' }}" href="{{ route('operador.salidas.index') }}"><i class="bi bi-box-arrow-right"></i> Salidas</a></li>
@endif

