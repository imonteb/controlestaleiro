<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaudeKitItem extends Model
{
    use HasFactory;

    protected $table = 'saude_kit_itens';

    protected $fillable = [
        'kit_id',
        'saude_item_id',
        'data_validade',
        'quantidade',
    ];

    protected $casts = [
        'data_validade' => 'date',
    ];

    public function kit()
    {
        return $this->belongsTo(AutoSocorroKit::class, 'kit_id');
    }

    public function item()
    {
        return $this->belongsTo(SaudeItem::class, 'saude_item_id');
    }
}
