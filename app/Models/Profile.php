<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Concerns\TracksAuditUsers;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasUuid, TracksAuditUsers;

    protected $fillable = [
        'uuid',
        'user_id',
        'avatar',
        'nickname',
        'bio',
        'gender',
        'language',
        'state',
        'country',
        'date_of_birth',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
