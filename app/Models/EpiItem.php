<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class EpiItem extends Model
{
    use HasFactory;

    protected $table = 'epi_items';

    protected $fillable = [
        'nombre',
        'talla',
        'codigo',
        'tipo',
        'descripcion',
        'requiere_talla',
        'tallas_disponibles',
        'campos_personalizados',
        'riscos',
        'ca_numero',
        'unidade',
        'stock_minimo',
        'activo',
        'motivo_baja',
    ];

    public const TIPO_INDIVIDUAL = 'individual';

    public const TIPO_COLETIVO = 'coletivo';

    protected $casts = [
        'activo' => 'boolean',
        'requiere_talla' => 'boolean',
        'tallas_disponibles' => 'array',
        'campos_personalizados' => 'array',
        'riscos' => 'array',
        'stock_minimo' => 'integer',
        'stock_por_talla' => 'array',
        'stock_total' => 'integer',
    ];

    public const RISCOS_DISPONIVEIS = [
        'Entalamento',
        'Esmagamento',
        'Choques, pancadas e compressões',
        'Queda em altura',
        'Queda ao mesmo nível',
        'Queda de objectos',
        'Projecção de partículas, objectos',
        'Cortes e perfurações',
        'Contacto directo com a electricidade',
        'Ruído',
        'Vibrações',
        'Frio',
        'Queimadura',
        'Radiações ionizantes',
        'Radiações não ionizantes',
        'Poeiras',
        'Gases e vapores',
        'Fumos',
        'Imersões',
        'Salpicos e projecções',
        'Atropelamento',
        'Substâncias Cancerígenas / Amianto',
        'Substâncias Irritantes / corrosivas',
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value)),
        );
    }

    public function rececoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpiRececao::class);
    }

    public function entregas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpiEntrega::class);
    }

    public function ajustes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpiAjuste::class);
    }

    /**
     * Calculate current stock, optionally filtered by tamanho.
     */
    public function stockAtual(): int
    {
        return (int) $this->stock_total;
    }

    /**
     * Get stock breakdown by tamanho.
     */
    public function stockPorTamanho(): array
    {
        if (! $this->requiere_talla || empty($this->tallas_disponibles)) {
            return ['' => $this->stockAtual()];
        }

        $stockPorTalla = $this->stock_por_talla ?? [];
        $stocks = [];
        foreach ($this->tallas_disponibles as $talla) {
            $stocks[$talla] = (int) ($stockPorTalla[$talla] ?? 0);
        }

        return $stocks;
    }

    public function stockBaixo(): bool
    {
        return $this->stockAtual() < $this->stock_minimo;
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
