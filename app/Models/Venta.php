<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'codigo_venta',
        'programacion_vuelo_id',
        'cliente_id',
        'reserva_id',
        'monto_total',
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

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'venta_id');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'venta_id');
    }

    public function devolucion()
    {
        return $this->hasOne(Devolucion::class, 'venta_id');
    }
}
