<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftTransaction extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'gift_id',
        'sender_id',
        'receiver_id',
        'room_id',
        'coin_cost',
        'reward_value',
        'context_type',
        'context_id',
    ];

    protected function casts(): array
    {
        return [
            'coin_cost' => 'integer',
            'reward_value' => 'integer',
        ];
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
