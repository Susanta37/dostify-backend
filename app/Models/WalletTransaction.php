<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\WalletTransactionCategory;
use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'wallet_id',
        'user_id',
        'type',
        'category',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => WalletTransactionType::class,
            'category' => WalletTransactionCategory::class,
            'amount' => 'integer',
            'balance_after' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
