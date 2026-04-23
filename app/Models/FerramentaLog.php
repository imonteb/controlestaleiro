<?php

namespace App\Models;

use App\Services\SignatureService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FerramentaLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ferramenta_id',
        'colaborador_id',
        'veiculo_id',
        'data_verificacao',
        'proxima_verificacao',
        'verificado_por',
        'num_registo_verificacao',
        'manutencao_tipo',
        'conclusao',
        'assinatura_path',
        'tipo_movimento',
        'checklist',
        'folha_id',
        'apto',
        'num_registo_seq',
        'unidade',
        'observacoes',
    ];

    protected $casts = [
        'data_verificacao' => 'date',
        'proxima_verificacao' => 'date',
        'checklist' => 'array',
        'apto' => 'boolean',
    ];

    public function ferramenta()
    {
        return $this->belongsTo(Ferramenta::class);
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Return assinatura_path as base64 data URI for display/printing,
     * regardless of whether it is a stored path or legacy raw base64.
     */
    protected function assinaturaPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value
                ? app(SignatureService::class)->toBase64($value)
                : null,
        );
    }
}
