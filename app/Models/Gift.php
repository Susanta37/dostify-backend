<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Concerns\TracksAuditUsers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    use HasUuid, TracksAuditUsers;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'animation_url',
        'icon_url',
        'coin_cost',
        'reward_value',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'coin_cost' => 'integer',
            'reward_value' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftTransaction::class);
    }
}
