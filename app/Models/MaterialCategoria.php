<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialCategoria extends Model
{
    use HasFactory;

    protected $table = 'material_categorias';

    protected $fillable = [
        'nome',
        'ordem',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function materiais(): HasMany
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
