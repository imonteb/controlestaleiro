<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Veiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'matricula',
        'activo',
        'motivo_baja',
        'link_seguros',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function driverLogs()
    {
        return $this->hasMany(VehicleDriverLog::class, 'vehicle_id');
    }

    public function currentDriver()
    {
        return $this->hasOne(VehicleDriverLog::class, 'vehicle_id')->whereNull('ended_at');
    }

    public function extintores()
    {
        return $this->hasMany(Extintor::class, 'veiculo_id');
    }

    public function kits()
    {
        return $this->hasMany(AutoSocorroKit::class, 'veiculo_id');
    }
}
