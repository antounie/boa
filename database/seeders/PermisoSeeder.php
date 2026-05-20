<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\RolPermiso;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['nombre' => 'usuarios',            'descripcion' => 'Gestión de usuarios del sistema'],
            ['nombre' => 'roles',               'descripcion' => 'Gestión de roles del sistema'],
            ['nombre' => 'permisos',            'descripcion' => 'Gestión de permisos por rol'],
            ['nombre' => 'aeropuertos',         'descripcion' => 'Gestión de aeropuertos'],
            ['nombre' => 'tipo_clases',         'descripcion' => 'Gestión de tipos de clase'],
            ['nombre' => 'aeronaves',           'descripcion' => 'Gestión de aeronaves'],
            ['nombre' => 'asientos',            'descripcion' => 'Gestión de asientos'],
            ['nombre' => 'rutas',               'descripcion' => 'Gestión de rutas aéreas'],
            ['nombre' => 'tramos',              'descripcion' => 'Gestión de tramos y sub-tramos'],
            ['nombre' => 'programacion_vuelos', 'descripcion' => 'Programación de vuelos'],
            ['nombre' => 'empleados',           'descripcion' => 'Gestión de empleados'],
            ['nombre' => 'tripulaciones',       'descripcion' => 'Gestión de tripulaciones'],
            ['nombre' => 'clientes',            'descripcion' => 'Gestión de clientes'],
            ['nombre' => 'ventas',              'descripcion' => 'Gestión de ventas'],
            ['nombre' => 'devoluciones',        'descripcion' => 'Gestión de devoluciones'],
            ['nombre' => 'reservas',            'descripcion' => 'Gestión de reservas'],
            ['nombre' => 'compras',             'descripcion' => 'Gestión de compras del cliente'],
            ['nombre' => 'tickets',             'descripcion' => 'Gestión de tickets'],
            ['nombre' => 'transacciones',       'descripcion' => 'Gestión de transacciones'],
            ['nombre' => 'salidas',             'descripcion' => 'Gestión de salidas'],
            ['nombre' => 'ingresos',            'descripcion' => 'Gestión de ingresos'],
            ['nombre' => 'egresos',             'descripcion' => 'Gestión de egresos'],
            ['nombre' => 'reportes',            'descripcion' => 'Acceso a reportes y estadísticas'],
        ];

        foreach ($permisos as $data) {
            Permiso::firstOrCreate(['nombre' => $data['nombre']], $data);
        }

        // Limpiar permiso obsoleto 'vuelos' (módulo eliminado)
        $vuelosObsoleto = Permiso::where('nombre', 'vuelos')->first();
        if ($vuelosObsoleto) {
            RolPermiso::where('permiso_id', $vuelosObsoleto->id)->delete();
            RolPermiso::where('tabla', 'vuelos')->delete();
            $vuelosObsoleto->delete();
        }

        // Vincular registros existentes en rol_permisos con su permiso_id correspondiente
        foreach (Permiso::all() as $permiso) {
            RolPermiso::where('tabla', $permiso->nombre)
                      ->whereNull('permiso_id')
                      ->update(['permiso_id' => $permiso->id]);
        }
    }
}
