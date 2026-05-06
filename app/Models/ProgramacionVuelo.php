<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AsientoProgramacion;

class ProgramacionVuelo extends Model
{
    protected $table = 'programacion_vuelos';

    protected $fillable = [
        'vuelo_id',
        'ruta_id',
        'aeronave_id',
        'fecha_salida',
        'hora_salida',
        'fecha_llegada',
        'hora_llegada',
        'precio_base',
        'asientos_vendidos',
        'estado',
    ];

    public function vuelo()
    {
        return $this->belongsTo(Vuelo::class, 'vuelo_id');
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function aeronave()
    {
        return $this->belongsTo(Aeronave::class, 'aeronave_id');
    }

    public function tripulacion()
    {
        return $this->hasMany(Tripulacion::class, 'programacion_vuelo_id');
    }

    public function salida()
    {
        return $this->hasOne(Salida::class, 'programacion_vuelo_id');
    }

    public function asientosProgramacion()
    {
        return $this->hasMany(AsientoProgramacion::class, 'programacion_vuelo_id');
    }
}