<?php

namespace App\Jobs;

use App\Models\GiftTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGiftTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public GiftTransaction $transaction) {}

    public function handle(): void
    {
        SendNotificationJob::dispatch(
            $this->transaction->receiver,
            'Gift Received',
            "You received a {$this->transaction->gift->name} gift!",
            ['transaction_uuid' => $this->transaction->uuid],
        );
    }
}
