<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolPermiso;

class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$tablas)
    {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // El administrador (rol_id = 1) siempre tiene acceso
        if ($usuario->rol_id === 1) {
            return $next($request);
        }

        // Verificar si el rol tiene acceso a alguna de las tablas requeridas
        foreach ($tablas as $tabla) {
            $tieneAcceso = RolPermiso::where('rol_id', $usuario->rol_id)
                ->where('tabla', $tabla)
                ->where('acceso', true)
                ->exists();

            if ($tieneAcceso) {
                return $next($request);
            }
        }

        // Si no tiene acceso, redirigir según su rol sin crear bucle
        $rutaActual = $request->path();

        if (str_starts_with($rutaActual, 'admin')) {
            if ($usuario->rol_id === 2) {
                return redirect()->route('operador.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            } elseif ($usuario->rol_id === 3) {
                return redirect()->route('cliente.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            }
        }

        if (str_starts_with($rutaActual, 'operador')) {
            if ($usuario->rol_id === 1 || str_contains($this->getDashboardRuta($usuario), 'admin')) {
                return redirect()->route('admin.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            } elseif ($usuario->rol_id === 3) {
                return redirect()->route('cliente.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            }
        }

        if (str_starts_with($rutaActual, 'cliente')) {
            if ($usuario->rol_id === 1 || str_contains($this->getDashboardRuta($usuario), 'admin')) {
                return redirect()->route('admin.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            } elseif ($usuario->rol_id === 2) {
                return redirect()->route('operador.dashboard')->with('error', 'No tiene permisos para acceder a ese módulo.');
            }
        }

        // Para roles personalizados, redirigir a su dashboard correspondiente
        $dashboard = $this->getDashboardRuta($usuario);
        return redirect()->route($dashboard)->with('error', 'No tiene permisos para acceder a ese módulo.');
    }

    private function getDashboardRuta($usuario)
    {
        if ($usuario->rol_id === 1) return 'admin.dashboard';
        if ($usuario->rol_id === 2) return 'operador.dashboard';
        if ($usuario->rol_id === 3) return 'cliente.dashboard';

        $permisos = RolPermiso::where('rol_id', $usuario->rol_id)
            ->where('acceso', true)
            ->pluck('tabla')
            ->toArray();

        $tablasAdmin = ['usuarios', 'roles', 'permisos', 'clientes', 'ventas', 'devoluciones', 'ingresos', 'egresos', 'reportes'];
        $tablasOperador = ['aeropuertos', 'tipo_clases', 'aeronaves', 'rutas', 'vuelos', 'programacion_vuelos', 'asientos', 'empleados', 'tripulaciones', 'salidas'];

        $accesoAdmin = count(array_intersect($permisos, $tablasAdmin));
        $accesoOperador = count(array_intersect($permisos, $tablasOperador));

        if ($accesoOperador >= $accesoAdmin) {
            return 'operador.dashboard';
        } elseif ($accesoAdmin > 0) {
            return 'admin.dashboard';
        }

        return 'cliente.dashboard';
    }
}