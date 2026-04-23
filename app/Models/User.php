<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\SignatureService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @mixin IdeHelperUser
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_number',
        'signature',
    ];

    protected $attributes = [
        'role' => '["operario"]',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'array',
        ];
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_EPI = 'epi';

    public const ROLE_LOGI = 'logi';

    public const ROLE_OPERARIO = 'operario';

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->role ?? [self::ROLE_OPERARIO], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN) || $this->isSuperAdmin();
    }

    public function isEpi(): bool
    {
        return $this->hasRole(self::ROLE_EPI);
    }

    public function isLogi(): bool
    {
        return $this->hasRole(self::ROLE_LOGI);
    }

    public function isOperario(): bool
    {
        return $this->hasRole(self::ROLE_OPERARIO);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    protected function signature(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value
                ? app(SignatureService::class)->toBase64($value)
                : null,
        );
    }

    public function vehicleLogs()
    {
        return $this->hasMany(VehicleDriverLog::class, 'user_id');
    }
}
