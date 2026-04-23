<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $fillable = [
        'nome',
        'telefone',
        'descricao',
        'activo',
        'ordem',
        'logo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ordem' => 'integer',
    ];
}
