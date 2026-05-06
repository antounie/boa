<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'nombre',
        'apellido',
        'cargo',
        'licencia',
        'estado',
    ];

    public function tripulaciones()
    {
        return $this->hasMany(Tripulacion::class, 'empleado_id');
    }
}