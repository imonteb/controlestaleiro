<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiais';

    protected $fillable = [
        'codigo',
        'nome',
        'descripcion',
        'categoria_id',
        'unidade_padrao',
        'activo',
        'motivo_baja',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public const UNIDADES = ['UN', 'M', 'KG', 'CX', 'L', 'M2', 'M3', 'ROL', 'PAR'];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(MaterialCategoria::class, 'categoria_id');
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
