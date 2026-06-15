<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoLog extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'video_session_id',
        'user_id',
        'event',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function videoSession(): BelongsTo
    {
        return $this->belongsTo(VideoSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
