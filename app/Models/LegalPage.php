<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $table = 'legal_pages';

    protected $fillable = [
        'slug',
        'titulo',
        'conteudo',
        'versao',
        'ultima_revisao',
        'publicada',
        'updated_by',
    ];

    protected $casts = [
        'ultima_revisao' => 'date',
        'publicada' => 'boolean',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublicada(Builder $query): Builder
    {
        return $query->where('publicada', true);
    }
}
