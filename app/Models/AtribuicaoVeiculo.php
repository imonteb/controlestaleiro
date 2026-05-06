<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class AtribuicaoVeiculo extends Model
{
    protected $table = 'asignacion_vehiculo';

    protected $fillable = [
        'asignacion_id',
        'vehiculo_id',
        'equipo_tipo',
    ];
}
