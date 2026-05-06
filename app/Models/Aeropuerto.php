<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aeropuerto extends Model
{
    protected $table = 'aeropuertos';

    protected $fillable = [
        'codigo_IATA',
        'nombre',
        'ciudad',
        'pais',
    ];

    public function rutasOrigen()
    {
        return $this->hasMany(Ruta::class, 'aeropuerto_origen_id');
    }

    public function rutasDestino()
    {
        return $this->hasMany(Ruta::class, 'aeropuerto_destino_id');
    }
}