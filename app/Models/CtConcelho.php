<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CtConcelho extends Model
{
    public $timestamps = false;

    protected $fillable = ['dd', 'cc', 'desig'];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(CtDistrito::class, 'dd', 'dd');
    }

    public function codigosPostais(): HasMany
    {
        return $this->hasMany(CtCodigoPostal::class, 'cc', 'cc')
            ->where('ct_codigos_postais.dd', $this->dd);
    }
}
