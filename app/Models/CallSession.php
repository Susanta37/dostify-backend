<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\CallStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallSession extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'caller_id',
        'callee_id',
        'status',
        'agora_channel',
        'agora_token',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected function casts(): array
    {
        return [
            'status' => CallStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_seconds' => 'integer',
        ];
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function callee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'callee_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }
}
