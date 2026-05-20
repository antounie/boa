<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolPermiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        $tablas = Permiso::orderBy('nombre')->pluck('nombre')->toArray();

        foreach ($roles as $rol) {
            $rol->permisos_data = RolPermiso::where('rol_id', $rol->id)
                ->pluck('acceso', 'tabla')
                ->toArray();
        }

        return view('admin.permisos.index', compact('roles', 'tablas'));
    }

    public function edit(Rol $rol)
    {
        $tablas = Permiso::orderBy('nombre')->pluck('nombre')->toArray();
        $permisosActuales = RolPermiso::where('rol_id', $rol->id)
            ->pluck('acceso', 'tabla')
            ->toArray();

        return view('admin.permisos.edit', compact('rol', 'tablas', 'permisosActuales'));
    }

    public function update(Request $request, Rol $rol)
    {
        $permisos = Permiso::all()->keyBy('nombre');
        $accesos = $request->input('acceso', []);

        foreach ($permisos as $nombre => $permiso) {
            RolPermiso::updateOrCreate(
                ['rol_id' => $rol->id, 'tabla' => $nombre],
                ['permiso_id' => $permiso->id, 'acceso' => in_array($nombre, $accesos)]
            );
        }

        return redirect()->route('admin.permisos.index')
            ->with('success', "Permisos del rol '{$rol->nombre}' actualizados exitosamente.");
    }
}
