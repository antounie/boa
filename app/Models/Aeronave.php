<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aeronave extends Model
{
    protected $table = 'aeronaves';

    protected $fillable = [
        'matricula',
        'modelo',
        'fabricante',
        'capacidad_total',
        'estado',
    ];

    public function asientos()
    {
        return $this->hasMany(Asiento::class, 'aeronave_id');
    }

    public function programaciones()
    {
        return $this->hasMany(ProgramacionVuelo::class, 'aeronave_id');
    }
}