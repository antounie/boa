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
            $permisosUsuario = collect();

            if (Auth::check()) {
                $usuario = Auth::user();

                $permisosUsuario = RolPermiso::where('rol_id', $usuario->rol_id)
                    ->where('acceso', true)
                    ->pluck('tabla');
            }

            $view->with('permisosUsuario', $permisosUsuario);
        });
    }
}
