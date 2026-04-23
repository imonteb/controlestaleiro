<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Atribuicao extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'fecha',
        'pep_id',
        'estado',
        'notas',
        'fecha_hora_evento',
        'descripcion_evento',
        'nombre_taller',
        'fecha_entrada_taller',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_hora_evento' => 'datetime',
        'fecha_entrada_taller' => 'date',
    ];

    public function pep()
    {
        return $this->belongsTo(Pep::class);
    }

    public function colaboradores()
    {
        return $this->belongsToMany(Colaborador::class, 'asignacion_colaborador', 'asignacion_id')
            ->withPivot('rol_en_equipo', 'equipo_tipo', 'es_jefe');
    }

    public function veiculos()
    {
        return $this->belongsToMany(Veiculo::class, 'asignacion_vehiculo', 'asignacion_id', 'vehiculo_id')
            ->withPivot('equipo_tipo');
    }
}
