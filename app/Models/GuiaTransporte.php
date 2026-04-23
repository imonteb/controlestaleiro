<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuiaTransporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'origem',
        // Origin (carga)
        'local_carga_nome',
        'local_carga_morada',
        'local_carga_localidade',
        'local_carga_cpostal',
        'local_carga_dd',
        'local_carga_cc',
        'data_inicio',
        'hora_inicio',
        // Vehicle
        'matricula',
        // Destination
        'destino_nome',
        'destino_morada',
        'destino_localidade',
        'destino_cpostal',
        'destino_dd',
        'destino_cc',
        'data_fim',
        'hora_fim',
        // Relations
        'user_id',
        'requerente_id',
        'processed_by_id',
        // Status & control
        'estado',
        'numero_at',
        'numero_oficial',
        'motivo_recusa',
        'data_emissao',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'data_emissao' => 'datetime',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePendentes($query)
    {
        return $query->where('estado', 'pendente');
    }

    public function scopeEmitidas($query)
    {
        return $query->where('estado', 'emitida');
    }

    public function scopeRecusadas($query)
    {
        return $query->where('estado', 'recusada');
    }

    public function scopeDeColaborador($query)
    {
        return $query->where('origem', 'colaborador');
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getOrigemLabelAttribute(): string
    {
        $parts = array_filter([
            $this->local_carga_localidade,
            $this->local_carga_morada,
        ]);

        return implode(', ', $parts) ?: '—';
    }

    public function getDestinoLabelAttribute(): string
    {
        $parts = array_filter([
            $this->destino_localidade,
            $this->destino_morada,
        ]);

        return implode(', ', $parts) ?: '—';
    }

    public function getCodigoPostalOrigemAttribute(): string
    {
        if (! $this->local_carga_cpostal) {
            return '';
        }

        return $this->local_carga_cpostal;
    }

    public function getCodigoPostalDestinoAttribute(): string
    {
        if (! $this->destino_cpostal) {
            return '';
        }

        return $this->destino_cpostal;
    }

    // ── Relations ─────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(GuiaItem::class, 'guia_transporte_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'requerente_id');
    }
}
