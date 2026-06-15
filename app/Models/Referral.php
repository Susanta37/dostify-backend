<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'referrer_id',
        'referred_id',
        'reward_coins',
        'is_rewarded',
        'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'reward_coins' => 'integer',
            'is_rewarded' => 'boolean',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
}
