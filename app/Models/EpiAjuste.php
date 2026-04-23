<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpiAjuste extends Model
{
    protected $table = 'epi_ajustes';

    protected $fillable = [
        'epi_item_id',
        'talla',
        'stock_sistema',
        'stock_real',
        'diferencia',
        'motivo',
        'ajustado_por',
    ];

    protected $casts = [
        'stock_sistema' => 'integer',
        'stock_real' => 'integer',
        'diferencia' => 'integer',
    ];

    public function epiItem()
    {
        return $this->belongsTo(EpiItem::class);
    }

    public function ajustadoPor()
    {
        return $this->belongsTo(User::class, 'ajustado_por');
    }
}
