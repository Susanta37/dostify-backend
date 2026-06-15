<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WalletResource;
use App\Http\Resources\Api\V1\WalletTransactionResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $wallet) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'wallet' => new WalletResource($this->wallet->getWallet($request->user())),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $wallet = $this->wallet->getWallet($request->user());

        $transactions = $wallet->transactions()
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'transactions' => WalletTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }
}
