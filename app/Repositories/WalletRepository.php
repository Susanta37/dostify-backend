<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\BaseRepository;

class WalletRepository extends BaseRepository
{
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    public function findByUser(User $user): ?Wallet
    {
        return Wallet::query()->where('user_id', $user->id)->first();
    }

    public function createForUser(User $user): Wallet
    {
        return Wallet::query()->create(['user_id' => $user->id]);
    }

    public function recordTransaction(Wallet $wallet, array $data): WalletTransaction
    {
        return $wallet->transactions()->create($data);
    }
}
