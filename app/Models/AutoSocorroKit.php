<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoSocorroKit extends Model
{
    use HasFactory;

    protected $table = 'auto_socorro_kits';

    protected $fillable = [
        'veiculo_id',
        'locacion_id',
        'designacao',
        'identificador_kit',
        'qr_code_token',
        'needs_restock',
    ];

    protected $casts = [
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

    public function itens()
    {
        return $this->hasMany(SaudeKitItem::class, 'kit_id');
    }
}
