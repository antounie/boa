<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visita;

class ContadorVisitas
{
    public function handle(Request $request, Closure $next)
    {
        $pagina = $request->path();
        $contador = Visita::registrar($pagina);

        view()->share('contadorVisitas', $contador);
        view()->share('paginaActual', $pagina);

        return $next($request);
    }
}