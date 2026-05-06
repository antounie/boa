<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Acceso completo al sistema, gestión de usuarios, roles, permisos, finanzas y reportes',
        ]);

        Rol::create([
            'nombre' => 'Operador',
            'descripcion' => 'Gestión de vuelos, programación, tripulación, rutas, aeronaves y salidas',
        ]);

        Rol::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Búsqueda de vuelos, reservas, compras de pasajes y gestión de tickets',
        ]);
    }
}
