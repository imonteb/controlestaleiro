<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPep
 */
class Pep extends Model
{
    protected $fillable = [
        'nombre',
        'locacion_id',
        'tipo_trabajo_id',
        'activo',
        'motivo_baja',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'locacion_id');
    }

    public function tipoTrabalho()
    {
        return $this->belongsTo(TipoTrabalho::class, 'tipo_trabajo_id');
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInativos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }
}
