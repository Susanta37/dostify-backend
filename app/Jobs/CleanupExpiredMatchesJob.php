<?php

namespace App\Jobs;

use App\Enums\MatchStatus;
use App\Models\MatchRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupExpiredMatchesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        MatchRequest::query()
            ->where('status', MatchStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => MatchStatus::Expired]);
    }
}
