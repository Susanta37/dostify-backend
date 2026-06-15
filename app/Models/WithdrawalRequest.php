<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'withdrawal_account_id',
        'amount',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => WithdrawalStatus::class,
            'reviewed_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawalAccount(): BelongsTo
    {
        return $this->belongsTo(WithdrawalAccount::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
