<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperDiaPublicado
 */
class DiaPublicado extends Model
{
    protected $table = 'dias_publicados';

    protected $fillable = ['fecha', 'activo_tv', 'confirmado_at', 'confirmado_por', 'firma_confirmacao'];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'activo_tv' => 'boolean',
            'confirmado_at' => 'datetime',
        ];
    }

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }

    public function isConfirmado(): bool
    {
        return $this->confirmado_at !== null;
    }

    /** Devolve o último dia publicado ainda não confirmado. */
    public static function pendenteConfirmacao(): ?self
    {
        return static::whereNull('confirmado_at')
            ->orderBy('fecha', 'desc')
            ->first();
    }

    /** Ativa este dia na TV e desativa todos os outros. */
    public function ativarNaTv(): void
    {
        static::where('id', '!=', $this->id)->update(['activo_tv' => false]);
        $this->update(['activo_tv' => true]);
    }

    /** Devolve o dia atualmente ativo na TV (ou null se não houver nenhum). */
    public static function ativoNaTv(): ?self
    {
        return static::where('activo_tv', true)->first();
    }
}
