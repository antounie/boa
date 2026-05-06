<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferenciaUsuario extends Model
{
    protected $table = 'preferencia_usuarios';

    protected $fillable = [
        'usuario_id',
        'tema',
        'modo_dia_noche',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
