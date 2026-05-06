<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\RolPermiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    private function getTablas()
    {
        return [
            'usuarios', 'roles', 'permisos', 'vuelos', 'programacion_vuelos',
            'rutas', 'aeropuertos', 'aeronaves', 'asientos', 'tipo_clases',
            'empleados', 'tripulaciones', 'clientes', 'reservas', 'ventas',
            'tickets', 'transacciones', 'salidas', 'devoluciones', 'ingresos',
            'egresos', 'reportes'
        ];
    }

    public function index()
    {
        $roles = Rol::all();
        $tablas = $this->getTablas();

        foreach ($roles as $rol) {
            $rol->permisos_data = RolPermiso::where('rol_id', $rol->id)
                ->pluck('acceso', 'tabla')
                ->toArray();
        }

        return view('admin.permisos.index', compact('roles', 'tablas'));
    }

    public function edit(Rol $rol)
    {
        $tablas = $this->getTablas();
        $permisosActuales = RolPermiso::where('rol_id', $rol->id)
            ->pluck('acceso', 'tabla')
            ->toArray();

        return view('admin.permisos.edit', compact('rol', 'tablas', 'permisosActuales'));
    }

    public function update(Request $request, Rol $rol)
    {
        $tablas = $this->getTablas();
        $accesos = $request->input('acceso', []);

        foreach ($tablas as $tabla) {
            RolPermiso::updateOrCreate(
                ['rol_id' => $rol->id, 'tabla' => $tabla],
                ['acceso' => in_array($tabla, $accesos) ? true : false]
            );
        }

        return redirect()->route('admin.permisos.index')
            ->with('success', "Permisos del rol '{$rol->nombre}' actualizados exitosamente.");
    }
}