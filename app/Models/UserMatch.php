<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMatch extends Model
{
    use HasUuid;

    protected $table = 'matches';

    protected $fillable = [
        'uuid',
        'user_one_id',
        'user_two_id',
        'match_request_one_id',
        'match_request_two_id',
        'status',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MatchStatus::class,
            'ended_at' => 'datetime',
        ];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }
}
