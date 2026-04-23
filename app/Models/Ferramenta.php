<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ferramenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'referencia',
        'designacao',
        'marca',
        'modelo',
        'num_serie',
        'familia',
        'localizacao',
        'periodicidade_meses',
        'tipo_documentacao',
        'estado_operacional',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(FerramentaLog::class);
    }

    public function ultimoLog()
    {
        return $this->hasOne(FerramentaLog::class)->latestOfMany();
    }

    public function getStatusPreventivoAttribute()
    {
        if ($this->estado_operacional === 'Abate') {
            return 'ABATIDO';
        }

        $ultimo = $this->ultimoLog;
        if (! $ultimo || ! $ultimo->proxima_verificacao) {
            return 'Sem registo';
        }

        $proxima = $ultimo->proxima_verificacao;
        $dias = (int) now()->diffInDays($proxima, false);

        if ($dias < 0) {
            return 'ATRASADO';
        }

        return $dias.' DIAS PARA VENCER';
    }
}
