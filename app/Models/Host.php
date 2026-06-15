<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Host extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'is_verified',
        'total_earnings',
        'total_sessions',
        'rating',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'total_earnings' => 'integer',
            'total_sessions' => 'integer',
            'rating' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
