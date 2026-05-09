<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $fillable = [
        'username',
        'email',
        'password',
        'nombre',
        'apellido',
        'estado',
        'intentos_fallidos',
        'veces_bloqueado',
        'bloqueado_hasta',
        'rol_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'rol_id'   => 'integer',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function preferencia()
    {
        return $this->hasOne(PreferenciaUsuario::class, 'usuario_id');
    }
}