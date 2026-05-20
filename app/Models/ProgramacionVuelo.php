<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramacionVuelo extends Model
{
    protected $table = 'programacion_vuelos';

    protected $fillable = [
        'codigo_vuelo',
        'ruta_id',
        'ruta_tramo_id',
        'aeronave_id',
        'aeropuerto_origen_id',
        'aeropuerto_destino_id',
        'fecha_salida',
        'hora_salida',
        'fecha_llegada',
        'hora_llegada',
        'precio_base',
        'asientos_vendidos',
        'estado',
        'fecha_original',
        'hora_original',
        'motivo_reprogramacion',
    ];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function rutaTramo()
    {
        return $this->belongsTo(RutaTramo::class, 'ruta_tramo_id');
    }

    public function aeronave()
    {
        return $this->belongsTo(Aeronave::class, 'aeronave_id');
    }

    public function aeropuertoOrigen()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_origen_id');
    }

    public function aeropuertoDestino()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_destino_id');
    }

    public function tripulacion()
    {
        return $this->hasMany(Tripulacion::class, 'programacion_vuelo_id');
    }

    public function salida()
    {
        return $this->hasOne(Salida::class, 'programacion_vuelo_id');
    }

    public function asientosProgramacion()
    {
        return $this->hasMany(AsientoProgramacion::class, 'programacion_vuelo_id');
    }

    public function precios()
    {
        return $this->hasMany(ProgramacionPrecio::class, 'programacion_vuelo_id');
    }

    public function getPrecioParaClase(int $tipoClaseId): float
    {
        $precio = $this->precios->firstWhere('tipo_clase_id', $tipoClaseId);
        if ($precio) return (float) $precio->precio;
        // fallback al precio_base * multiplicador si no hay precio configurado
        $clase = $this->precios->first();
        return (float) ($this->precio_base ?? 0);
    }
}
