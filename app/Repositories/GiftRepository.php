<?php

namespace App\Repositories;

use App\Models\Gift;
use App\Models\GiftTransaction;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class GiftRepository extends BaseRepository
{
    public function __construct(Gift $model)
    {
        parent::__construct($model);
    }

    public function getActive(): Collection
    {
        return Gift::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function createTransaction(array $data): GiftTransaction
    {
        return GiftTransaction::query()->create($data);
    }
}
