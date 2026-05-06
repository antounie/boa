<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'codigo_reserva',
        'programacion_vuelo_id',
        'cliente_id',
        'asiento_id',
        'monto',
        'estado',
    ];

    public function programacionVuelo()
    {
        return $this->belongsTo(ProgramacionVuelo::class, 'programacion_vuelo_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function asiento()
    {
        return $this->belongsTo(Asiento::class, 'asiento_id');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'reserva_id');
    }
}