<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoClase extends Model
{
    protected $table = 'tipo_clases';

    protected $fillable = [
        'nombre',
        'multiplicador_precio',
        'descripcion',
        'caracteristicas',
    ];

    public function asientos()
    {
        return $this->hasMany(Asiento::class, 'tipo_clase_id');
    }
}