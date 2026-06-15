<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Concerns\TracksAuditUsers;
use App\Enums\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasUuid, TracksAuditUsers;

    protected $fillable = [
        'uuid',
        'host_id',
        'title',
        'description',
        'type',
        'status',
        'agora_channel',
        'max_listeners',
        'scheduled_at',
        'started_at',
        'ended_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => RoomType::class,
            'max_listeners' => 'integer',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(RoomMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(RoomMessage::class);
    }
}
