<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table = 'visitas';

    protected $fillable = [
        'pagina',
        'contador',
    ];

    public static function registrar($pagina)
    {
        $visita = self::firstOrCreate(
            ['pagina' => $pagina],
            ['contador' => 0]
        );
        $visita->increment('contador');
        return $visita->contador;
    }
}