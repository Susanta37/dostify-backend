<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'fcm_token',
        'ip_address',
        'last_active_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'device_type' => DeviceType::class,
            'last_active_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
