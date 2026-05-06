<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\RolPermiso;

class PermisosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $usuario = Auth::user();

                // Administrador tiene acceso a todo
                if ($usuario->rol_id === 1) {
                    $permisosUsuario = collect([
                        'usuarios', 'roles', 'permisos', 'vuelos', 'programacion_vuelos',
                        'rutas', 'aeropuertos', 'aeronaves', 'asientos', 'tipo_clases',
                        'empleados', 'tripulaciones', 'clientes', 'reservas', 'ventas',
                        'tickets', 'transacciones', 'salidas', 'devoluciones', 'ingresos',
                        'egresos', 'reportes'
                    ]);
                } else {
                    $permisosUsuario = RolPermiso::where('rol_id', $usuario->rol_id)
                        ->where('acceso', true)
                        ->pluck('tabla');
                }

                $view->with('permisosUsuario', $permisosUsuario);
            }
        });
    }
}
