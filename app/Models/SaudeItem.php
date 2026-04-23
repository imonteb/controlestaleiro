<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaudeItem extends Model
{
    use HasFactory;

    protected $table = 'saude_itens';

    protected $fillable = [
        'nombre',
        'unidade',
    ];
}
