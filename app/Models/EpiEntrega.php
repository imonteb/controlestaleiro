<?php

namespace App\Models;

use App\Enums\EpiEntregaEstado;
use App\Services\SignatureService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class EpiEntrega extends Model
{
    protected $table = 'epi_entregas';

    protected $fillable = [
        'epi_item_id',
        'colaborador_id',
        'cantidad',
        'talla',
        'valores_personalizados',
        'fecha_entrega',
        'fecha_devolucion',
        'estado',
        'firma',
        'firma_devolucion',
        'observaciones',
        'entregado_por',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_devolucion' => 'date',
        'cantidad' => 'integer',
        'valores_personalizados' => 'array',
        'estado' => EpiEntregaEstado::class,
    ];

    public function epiItem()
    {
        return $this->belongsTo(EpiItem::class);
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function entregadoPor()
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    /**
     * Return the firma as a base64 data URI, regardless of whether
     * it is stored as a file path or as raw base64 (legacy records).
     */
    protected function firma(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value
                ? app(SignatureService::class)->toBase64($value)
                : null,
        );
    }

    protected function firmaDevolucion(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value
                ? app(SignatureService::class)->toBase64($value)
                : null,
        );
    }
}
