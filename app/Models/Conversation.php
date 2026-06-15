<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\ConversationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'type',
        'name',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConversationType::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
