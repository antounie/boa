<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaTramo extends Model
{
    protected $table = 'ruta_tramo';

    protected $fillable = ['ruta_id', 'tramo_id', 'orden'];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function tramo()
    {
        return $this->belongsTo(Tramo::class, 'tramo_id');
    }

    public function programaciones()
    {
        return $this->hasMany(ProgramacionVuelo::class, 'ruta_tramo_id');
    }
}
