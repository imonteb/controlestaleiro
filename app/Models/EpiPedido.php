<?php

namespace App\Models;

use App\Enums\EpiPedidoEstado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpiPedido extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'estado' => EpiPedidoEstado::class,
        ];
    }

    protected $fillable = [
        'colaborador_id',
        'epi_item_id',
        'tamanho',
        'quantidade',
        'estado',
        'motivo_colaborador',
        'motivo_admin',
    ];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function epiItem()
    {
        return $this->belongsTo(EpiItem::class);
    }
}
