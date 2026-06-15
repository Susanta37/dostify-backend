<?php

namespace App\Services;

use App\Enums\WalletTransactionCategory;
use App\Enums\WalletTransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(private WalletRepository $wallets) {}

    public function getWallet(User $user): Wallet
    {
        return $this->wallets->findByUser($user)
            ?? $this->wallets->createForUser($user);
    }

    public function credit(
        User $user,
        int $amount,
        WalletTransactionCategory $category,
        ?string $description = null,
        ?object $reference = null,
    ) {
        return $this->transact($user, WalletTransactionType::Credit, $amount, $category, $description, $reference);
    }

    public function debit(
        User $user,
        int $amount,
        WalletTransactionCategory $category,
        ?string $description = null,
        ?object $reference = null,
    ) {
        return $this->transact($user, WalletTransactionType::Debit, $amount, $category, $description, $reference);
    }

    private function transact(
        User $user,
        WalletTransactionType $type,
        int $amount,
        WalletTransactionCategory $category,
        ?string $description,
        ?object $reference,
    ) {
        return DB::transaction(function () use ($user, $type, $amount, $category, $description, $reference) {
            $wallet = Wallet::query()->lockForUpdate()->firstOrCreate(
                ['user_id' => $user->id],
                ['coin_balance' => 0, 'reward_balance' => 0]
            );

            if ($type === WalletTransactionType::Debit && $wallet->coin_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient coin balance.'],
                ]);
            }

            $newBalance = $type === WalletTransactionType::Credit
                ? $wallet->coin_balance + $amount
                : $wallet->coin_balance - $amount;

            $wallet->update(['coin_balance' => $newBalance]);

            return $this->wallets->recordTransaction($wallet, [
                'user_id' => $user->id,
                'type' => $type,
                'category' => $category,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_type' => $reference ? $reference::class : null,
                'reference_id' => $reference?->id,
            ]);
        });
    }
}
