<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CtDistrito extends Model
{
    public $timestamps = false;

    protected $fillable = ['dd', 'desig'];

    public function concelhos(): HasMany
    {
        return $this->hasMany(CtConcelho::class, 'dd', 'dd');
    }

    public function codigosPostais(): HasMany
    {
        return $this->hasMany(CtCodigoPostal::class, 'dd', 'dd');
    }
}
