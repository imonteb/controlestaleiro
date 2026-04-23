<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDriverLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'colaborador_id',
        'started_at',
        'ended_at',
        'takeover_from_name',
        'takeover_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'takeover_at' => 'datetime',
    ];

    /** Sessão ativa atual para um veículo (sem ended_at). */
    public static function ativoParaVeiculo(int $vehicleId): ?self
    {
        return static::where('vehicle_id', $vehicleId)->whereNull('ended_at')->first();
    }

    /** Sessão ativa atual para um colaborador (sem ended_at). */
    public static function ativoParaColaborador(int $colaboradorId, ?int $excluirVehicleId = null): ?self
    {
        return static::where('colaborador_id', $colaboradorId)
            ->whereNull('ended_at')
            ->when($excluirVehicleId, fn ($q) => $q->where('vehicle_id', '!=', $excluirVehicleId))
            ->first();
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'vehicle_id');
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }
}
