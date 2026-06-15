<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'uuid',
    'name',
    'email',
    'phone',
    'password',
    'role',
    'is_profile_complete',
    'is_active',
    'referral_code',
    'referred_by',
    'phone_verified_at',
    'last_seen_at',
    'created_by',
    'updated_by',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuid, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected static function bootHasUuid(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }

            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_profile_complete' => 'boolean',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function host(): HasOne
    {
        return $this->hasOne(Host::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class);
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }

    public function isHost(): bool
    {
        return $this->role === UserRole::Host || $this->host !== null;
    }

    public function isBanned(): bool
    {
        return $this->bans()->where('is_active', true)->get()->contains(
            fn (Ban $ban) => $ban->isCurrentlyActive()
        );
    }
}
