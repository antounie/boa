<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'salidas';

    protected $fillable = [
        'programacion_vuelo_id',
        'monto_total_recaudado',
    ];

    public function programacionVuelo()
    {
        return $this->belongsTo(ProgramacionVuelo::class, 'programacion_vuelo_id');
    }

    public function ingreso()
    {
        return $this->hasOne(Ingreso::class, 'salida_id');
    }
}