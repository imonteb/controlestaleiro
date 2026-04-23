<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalFrequente extends Model
{
    protected $table = 'locais_frequentes';

    protected $fillable = [
        'nome', 'tipo', 'morada', 'localidade',
        'cp4', 'cp3', 'cpalf', 'codigo_postal_id',
        'pais', 'activo', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function codigoPostal(): BelongsTo
    {
        return $this->belongsTo(CtCodigoPostal::class, 'codigo_postal_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function codigoPostalCompleto(): string
    {
        if ($this->cp4 && $this->cp3) {
            return $this->cp4.'-'.$this->cp3;
        }

        return '';
    }
}
