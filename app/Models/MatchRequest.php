<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRequest extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'preferred_language',
        'preferred_gender',
        'match_type',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MatchStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
