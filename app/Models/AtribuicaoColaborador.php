<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class AtribuicaoColaborador extends Model
{
    protected $table = 'asignacion_colaborador';

    protected $fillable = [
        'asignacion_id',
        'colaborador_id',
        'rol_en_equipo',
        'equipo_tipo',
        'es_jefe',
    ];
}
