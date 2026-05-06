<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';

    protected $fillable = [
        'aeropuerto_origen_id',
        'aeropuerto_destino_id',
        'distancia',
        'duracion_estimada',
        'tipo',
    ];

    public function aeropuertoOrigen()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_origen_id');
    }

    public function aeropuertoDestino()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_destino_id');
    }

    public function programaciones()
    {
        return $this->hasMany(ProgramacionVuelo::class, 'ruta_id');
    }
}