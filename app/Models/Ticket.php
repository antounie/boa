<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'numero_ticket',
        'venta_id',
        'asiento_id',
        'sub_tramo_id',
        'estado',
        'pasajero_nombre',
        'pasajero_apellido',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function asiento()
    {
        return $this->belongsTo(Asiento::class, 'asiento_id');
    }

    public function subTramo()
    {
        return $this->belongsTo(Tramo::class, 'sub_tramo_id');
    }

    public function esParcial(): bool
    {
        return !is_null($this->sub_tramo_id);
    }
}
