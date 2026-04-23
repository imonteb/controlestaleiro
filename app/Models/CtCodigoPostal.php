<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CtCodigoPostal extends Model
{
    public $timestamps = false;

    protected $table = 'ct_codigos_postais';

    protected $fillable = [
        'dd', 'cc', 'llll', 'localidade',
        'art_cod', 'art_tipo', 'pri_prep', 'art_titulo', 'seg_prep',
        'art_desig', 'art_local', 'troco', 'porta', 'cliente',
        'cp4', 'cp3', 'cpalf', 'nome_arteria',
    ];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(CtDistrito::class, 'dd', 'dd');
    }

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(CtConcelho::class, 'cc', 'cc');
    }

    public function codigoPostalCompleto(): string
    {
        return $this->cp4.'-'.$this->cp3;
    }
}
