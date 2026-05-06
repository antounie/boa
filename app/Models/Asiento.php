<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    protected $table = 'asientos';

    protected $fillable = [
        'aeronave_id',
        'numero',
        'fila',
        'tipo_clase_id',
    ];

    public function aeronave()
    {
        return $this->belongsTo(Aeronave::class, 'aeronave_id');
    }

    public function tipoClase()
    {
        return $this->belongsTo(TipoClase::class, 'tipo_clase_id');
    }
}