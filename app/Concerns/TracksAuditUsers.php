<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait TracksAuditUsers
{
    protected static function bootTracksAuditUsers(): void
    {
        static::creating(function (Model $model): void {
            if (Auth::check() && $model->isFillable('created_by')) {
                $model->created_by ??= Auth::id();
            }

            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->updated_by ??= Auth::id();
            }
        });

        static::updating(function (Model $model): void {
            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
