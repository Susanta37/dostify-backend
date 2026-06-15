<?php

namespace App\Models;

use App\Enums\RoomMemberRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMember extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => RoomMemberRole::class,
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
