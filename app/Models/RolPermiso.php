<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $table = 'rol_permisos';

    protected $fillable = [
        'rol_id',
        'permiso_id',
        'tabla',
        'acceso',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }
}