<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transacciones';

    protected $fillable = [
        'referencia',
        'monto',
        'metodo_pago',
        'estado',
    ];

    public function venta()
    {
        return $this->hasOne(Venta::class, 'transaccion_id');
    }
}