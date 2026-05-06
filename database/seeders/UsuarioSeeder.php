<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'username' => 'admin',
            'email' => 'admin@boa.com.bo',
            'password' => 'Admin2026$',
            'nombre' => 'Jose Antonio',
            'apellido' => 'Arnez Choque',
            'estado' => 'Activo',
            'intentos_fallidos' => 0,
            'rol_id' => 1,
        ]);
    }
}