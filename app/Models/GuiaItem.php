<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuiaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'guia_transporte_id',
        'descricao',
        'quantidade',
        'unidade',
    ];

    public function guia(): BelongsTo
    {
        return $this->belongsTo(GuiaTransporte::class, 'guia_transporte_id');
    }
}
