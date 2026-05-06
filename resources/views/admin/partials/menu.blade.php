<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

@if($permisosUsuario->contains('usuarios'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}"><i class="bi bi-people"></i> Usuarios</a></li>
@endif

@if($permisosUsuario->contains('roles'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock"></i> Roles</a></li>
@endif

@if($permisosUsuario->contains('permisos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}" href="{{ route('admin.permisos.index') }}"><i class="bi bi-key"></i> Permisos</a></li>
@endif

@if($permisosUsuario->contains('clientes'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}" href="{{ route('admin.clientes.index') }}"><i class="bi bi-person-lines-fill"></i> Clientes</a></li>
@endif

@if($permisosUsuario->contains('ventas'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ventas.*') ? 'active' : '' }}" href="{{ route('admin.ventas.index') }}"><i class="bi bi-cart-check"></i> Ventas</a></li>
@endif

@if($permisosUsuario->contains('devoluciones'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.devoluciones.*') ? 'active' : '' }}" href="{{ route('admin.devoluciones.index') }}"><i class="bi bi-arrow-return-left"></i> Devoluciones</a></li>
@endif

@if($permisosUsuario->contains('ingresos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ingresos.*') ? 'active' : '' }}" href="{{ route('admin.ingresos.index') }}"><i class="bi bi-graph-up-arrow"></i> Ingresos</a></li>
@endif

@if($permisosUsuario->contains('egresos'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.egresos.*') ? 'active' : '' }}" href="{{ route('admin.egresos.index') }}"><i class="bi bi-graph-down-arrow"></i> Egresos</a></li>
@endif

@if($permisosUsuario->contains('reportes'))
<li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" href="{{ route('admin.reportes.index') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
@endif