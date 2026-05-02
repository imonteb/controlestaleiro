<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Localizacao extends Model
{
    protected $table = 'locaciones';

    protected $fillable = ['nombre'];

    public function peps()
    {
        return $this->hasMany(Pep::class, 'locacion_id');
    }

    public function extintores()
    {
        return $this->hasMany(Extintor::class, 'locacion_id');
    }

    public function kits()
    {
        return $this->hasMany(AutoSocorroKit::class, 'locacion_id');
    }
}
