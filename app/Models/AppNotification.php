<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'colaborador_id',
        'tipo',
        'titulo',
        'mensagem',
        'data_expiracao',
        'activa',
    ];

    protected $casts = [
        'data_expiracao' => 'datetime',
        'activa' => 'boolean',
    ];
}
