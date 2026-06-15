<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\OtpPurpose;
use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'phone',
        'otp_hash',
        'purpose',
        'expires_at',
        'verified_at',
        'ip_address',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => OtpPurpose::class,
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
