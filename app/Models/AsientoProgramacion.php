<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsientoProgramacion extends Model
{
    protected $table = 'asiento_programacion';

    protected $fillable = [
        'asiento_id',
        'programacion_vuelo_id',
        'estado',
    ];

    public function asiento()
    {
        return $this->belongsTo(Asiento::class, 'asiento_id');
    }

    public function programacionVuelo()
    {
        return $this->belongsTo(ProgramacionVuelo::class, 'programacion_vuelo_id');
    }
}