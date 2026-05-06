<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tripulacion extends Model
{
    protected $table = 'tripulaciones';

    protected $fillable = [
        'programacion_vuelo_id',
        'empleado_id',
        'cargo',
    ];

    public function programacionVuelo()
    {
        return $this->belongsTo(ProgramacionVuelo::class, 'programacion_vuelo_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}