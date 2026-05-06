<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vuelo extends Model
{
    protected $table = 'vuelos';

    protected $fillable = [
        'codigo_vuelo',
        'tipo',
        'estado',
        'vuelo_padre_id',
    ];

    // Relación recursiva: vuelo padre
    public function vueloPadre()
    {
        return $this->belongsTo(Vuelo::class, 'vuelo_padre_id');
    }

    // Relación recursiva: vuelos hijos (escalas)
    public function escalas()
    {
        return $this->hasMany(Vuelo::class, 'vuelo_padre_id');
    }

    public function programaciones()
    {
        return $this->hasMany(ProgramacionVuelo::class, 'vuelo_id');
    }
}