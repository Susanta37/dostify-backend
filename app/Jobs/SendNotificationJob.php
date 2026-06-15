<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $title,
        public string $body,
        public array $data = [],
    ) {}

    public function handle(): void
    {
        // TODO: Integrate Firebase FCM
        logger()->info('Notification queued', [
            'user_id' => $this->user->id,
            'title' => $this->title,
            'body' => $this->body,
        ]);
    }
}
