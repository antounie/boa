<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = [
        'salida_id',
        'programacion_vuelo_id',
        'monto_total',
        'cantidad_pasajes',
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

    public function programacionVuelo()
    {
        return $this->belongsTo(ProgramacionVuelo::class, 'programacion_vuelo_id');
    }
}