<?php

namespace App\Services;

use App\Enums\WalletTransactionCategory;
use App\Models\Gift;
use App\Models\User;
use App\Repositories\GiftRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GiftService
{
    public function __construct(
        private GiftRepository $gifts,
        private WalletService $wallet,
    ) {}

    public function catalog()
    {
        return $this->gifts->getActive();
    }

    public function send(User $sender, User $receiver, Gift $gift, ?int $roomId = null, ?string $contextType = null, ?int $contextId = null)
    {
        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'receiver' => ['You cannot send a gift to yourself.'],
            ]);
        }

        return DB::transaction(function () use ($sender, $receiver, $gift, $roomId, $contextType, $contextId) {
            $this->wallet->debit(
                $sender,
                $gift->coin_cost,
                WalletTransactionCategory::GiftSent,
                "Gift sent: {$gift->name}",
                $gift,
            );

            if ($gift->reward_value > 0) {
                $this->wallet->credit(
                    $receiver,
                    $gift->reward_value,
                    WalletTransactionCategory::GiftReceived,
                    "Gift received: {$gift->name}",
                    $gift,
                );
            }

            return $this->gifts->createTransaction([
                'gift_id' => $gift->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'room_id' => $roomId,
                'coin_cost' => $gift->coin_cost,
                'reward_value' => $gift->reward_value,
                'context_type' => $contextType,
                'context_id' => $contextId,
            ]);
        });
    }
}
