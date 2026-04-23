<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extintor extends Model
{
    use HasFactory;

    protected $table = 'extintores';

    protected $fillable = [
        'num_serie',
        'qr_code_token',
        'tipo_agente',
        'tamanho',
        'estado',
        'needs_restock',
        'data_verificacao',
        'proxima_revisao',
        'veiculo_id',
        'locacion_id',
    ];

    protected $casts = [
        'data_verificacao' => 'date',
        'proxima_revisao' => 'date',
        'needs_restock' => 'boolean',
    ];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'locacion_id');
    }
}
