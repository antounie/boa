<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProgramacionVuelo;

class Tramo extends Model
{
    protected $table = 'tramos';

    protected $fillable = [
        'tramo_padre_id',
        'aeropuerto_origen_id',
        'aeropuerto_destino_id',
        'duracion_estimada',
        'tiempo_escala',
        'orden',
    ];

    public function recalcularDuracion(): void
    {
        $totalMinutos = $this->subTramos()->get()->sum(function ($sub) {
            return $this->timeToMinutes($sub->duracion_estimada)
                 + $this->timeToMinutes($sub->tiempo_escala);
        });

        if ($totalMinutos > 0) {
            $nuevaDuracion = sprintf('%02d:%02d', intdiv($totalMinutos, 60), $totalMinutos % 60);
            $this->update(['duracion_estimada' => $nuevaDuracion]);
            $this->sincronizarLlegadaProgramaciones($nuevaDuracion);
        }
    }

    private function sincronizarLlegadaProgramaciones(string $duracion): void
    {
        [$h, $m] = array_map('intval', explode(':', $duracion));

        $rutaTramoIds = \App\Models\RutaTramo::where('tramo_id', $this->id)->pluck('id');

        ProgramacionVuelo::whereIn('ruta_tramo_id', $rutaTramoIds)
            ->where('estado', 'Programado')
            ->get()
            ->each(function ($prog) use ($h, $m) {
                $llegada = \Carbon\Carbon::parse($prog->fecha_salida . ' ' . $prog->hora_salida)
                    ->addHours($h)->addMinutes($m);
                $prog->update([
                    'fecha_llegada' => $llegada->toDateString(),
                    'hora_llegada'  => $llegada->format('H:i'),
                ]);
            });
    }

    private function timeToMinutes(?string $time): int
    {
        if (!$time) return 0;
        [$h, $m] = array_map('intval', explode(':', $time));
        return $h * 60 + $m;
    }

    public function tramoPadre()
    {
        return $this->belongsTo(Tramo::class, 'tramo_padre_id');
    }

    public function subTramos()
    {
        return $this->hasMany(Tramo::class, 'tramo_padre_id')->orderBy('orden');
    }

    public function aeropuertoOrigen()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_origen_id');
    }

    public function aeropuertoDestino()
    {
        return $this->belongsTo(Aeropuerto::class, 'aeropuerto_destino_id');
    }

    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'ruta_tramo')->withPivot('orden');
    }

    public function esPadre(): bool
    {
        return is_null($this->tramo_padre_id);
    }

    public function tieneEscalas(): bool
    {
        return $this->subTramos()->exists();
    }
}
