<?php

namespace App\Repositories;

use App\Models\CoinPackage;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class CoinPackageRepository extends BaseRepository
{
    public function __construct(CoinPackage $model)
    {
        parent::__construct($model);
    }

    public function getActive(): Collection
    {
        return CoinPackage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
