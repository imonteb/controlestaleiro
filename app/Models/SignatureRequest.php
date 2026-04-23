<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignatureRequest extends Model
{
    protected $fillable = [
        'token',
        'signature_data',
        'status',
        'signable_type',
        'signable_id',
        'metadata',
        'ip_address',
        'user_agent',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function signable()
    {
        return $this->morphTo();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || ($this->expires_at && $this->expires_at->isPast());
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
