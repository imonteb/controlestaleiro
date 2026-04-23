<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    protected $fillable = [
        'colaborador_id',
        'tipo',
        'data_hora',
        'localizacao',
        'descricao',
        'photo_path',
        'estado',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }
}
