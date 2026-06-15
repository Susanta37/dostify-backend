<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CoinPackageResource;
use App\Repositories\CoinPackageRepository;
use Illuminate\Http\JsonResponse;

class CoinPackageController extends Controller
{
    public function __construct(private CoinPackageRepository $packages) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'packages' => CoinPackageResource::collection($this->packages->getActive()),
        ]);
    }
}
