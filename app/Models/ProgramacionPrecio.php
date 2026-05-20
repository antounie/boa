<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramacionPrecio extends Model
{
    protected $fillable = ['programacion_vuelo_id', 'tipo_clase_id', 'precio'];

    public function tipoClase()
    {
        return $this->belongsTo(TipoClase::class);
    }
}
