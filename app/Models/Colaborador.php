<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperColaborador
 * @mixin \Eloquent
 */
class Colaborador extends Model
{
    use HasFactory;

    protected $table = 'colaboradores';

    protected $fillable = [
        'numero_colaborador',
        'nombre',
        'apellido',
        'telefono',
        'pin',
        'denominacion_cargo',
        'activo',
        'motivo_baja',
        'visible_en_dashboard',
        'terms_accepted_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'visible_en_dashboard' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'pin' => 'hashed',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value)),
        );
    }

    protected function apellido(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value)),
        );
    }

    protected function denominacionCargo(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value)),
        );
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
