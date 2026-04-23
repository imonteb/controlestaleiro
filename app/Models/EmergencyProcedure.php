<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyProcedure extends Model
{
    protected $fillable = [
        'titulo',
        'subtitulo',
        'conteudo',
        'tipo',
        'activo',
        'ordem',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
