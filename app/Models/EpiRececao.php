<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class EpiRececao extends Model
{
    protected $table = 'epi_recepciones';

    protected $fillable = [
        'epi_item_id',
        'cantidad',
        'talla',
        'fecha',
        'proveedor',
        'numero_factura',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'integer',
    ];

    public function epiItem()
    {
        return $this->belongsTo(EpiItem::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
