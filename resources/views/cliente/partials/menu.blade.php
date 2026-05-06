<li class="nav-item"><a class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}" href="{{ route('cliente.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

@if($permisosUsuario->contains('reservas') || $permisosUsuario->contains('ventas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('cliente.buscar*') ? 'active' : '' }}" href="{{ route('cliente.buscar') }}"><i class="bi bi-search"></i> Buscar Vuelos</a></li>
@endif

@if($permisosUsuario->contains('reservas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('cliente.mis.reservas') ? 'active' : '' }}" href="{{ route('cliente.mis.reservas') }}"><i class="bi bi-bookmark"></i> Mis Reservas</a></li>
@endif

@if($permisosUsuario->contains('ventas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('cliente.mis.compras') ? 'active' : '' }}" href="{{ route('cliente.mis.compras') }}"><i class="bi bi-cart-check"></i> Mis Compras</a></li>
@endif

@if($permisosUsuario->contains('tickets'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('cliente.mis.tickets') ? 'active' : '' }}" href="{{ route('cliente.mis.tickets') }}"><i class="bi bi-ticket-perforated"></i> Mis Tickets</a></li>
@endif