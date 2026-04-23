<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class TipoTrabalho extends Model
{
    protected $table = 'tipos_trabajo';

    protected $fillable = ['nombre', 'color'];

    public function peps()
    {
        return $this->hasMany(Pep::class, 'tipo_trabajo_id');
    }
}
