<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperAvisoTv
 */
class AvisoTv extends Model
{
    protected $table = 'avisos_tv';

    protected $fillable = [
        'titulo',
        'conteudo',
        'imagem',
        'layout_imagem',
        'cor',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function imageUrl(): ?string
    {
        return $this->imagem ? Storage::url($this->imagem) : null;
    }

    public static function ativos()
    {
        return static::where('ativo', true)->orderBy('ordem')->orderBy('id')->get();
    }

    public function corEstilo(): array
    {
        return match ($this->cor) {
            'verde' => ['bg' => 'rgba(16,185,129,0.12)',   'border' => 'rgba(16,185,129,0.35)',   'text' => '#6ee7b7'],
            'amarelo' => ['bg' => 'rgba(234,179,8,0.12)',    'border' => 'rgba(234,179,8,0.35)',    'text' => '#fde047'],
            'vermelho' => ['bg' => 'rgba(239,68,68,0.12)',    'border' => 'rgba(239,68,68,0.35)',    'text' => '#fca5a5'],
            'roxo' => ['bg' => 'rgba(139,92,246,0.12)',   'border' => 'rgba(139,92,246,0.35)',   'text' => '#c4b5fd'],
            'branco' => ['bg' => 'rgba(255,255,255,0.08)',  'border' => 'rgba(255,255,255,0.25)',  'text' => '#ffffff'],
            default => ['bg' => 'rgba(59,130,246,0.12)',   'border' => 'rgba(59,130,246,0.35)',   'text' => '#93c5fd'],  // azul
        };
    }
}
