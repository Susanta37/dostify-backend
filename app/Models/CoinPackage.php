<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Concerns\TracksAuditUsers;
use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    use HasUuid, TracksAuditUsers;

    protected $fillable = [
        'uuid',
        'name',
        'coins',
        'price',
        'currency',
        'discount_percent',
        'is_active',
        'is_promotional',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'coins' => 'integer',
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'is_active' => 'boolean',
            'is_promotional' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
