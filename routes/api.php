<?php

use App\Http\Controllers\Api\V1\AgoraController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Call\AudioCallController;
use App\Http\Controllers\Api\V1\Call\VideoCallController;
use App\Http\Controllers\Api\V1\Chat\ConversationController;
use App\Http\Controllers\Api\V1\Chat\MessageController;
use App\Http\Controllers\Api\V1\CoinPackageController;
use App\Http\Controllers\Api\V1\GiftController;
use App\Http\Controllers\Api\V1\Match\MatchController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('otp/request', [AuthController::class, 'requestOtp'])
            ->middleware('throttle:otp-request');
        Route::post('otp/verify', [AuthController::class, 'verifyOtp'])
            ->middleware('throttle:otp-verify');
        Route::post('google', [AuthController::class, 'googleLogin'])
            ->middleware('throttle:google-login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);

        Route::get('wallet', [WalletController::class, 'show']);
        Route::get('wallet/transactions', [WalletController::class, 'transactions']);

        Route::get('coin-packages', [CoinPackageController::class, 'index']);

        Route::get('gifts', [GiftController::class, 'index']);
        Route::post('gifts/send', [GiftController::class, 'send']);

        // Phase 1.5 - Matching
        Route::prefix('match')->group(function () {
            Route::post('request', [MatchController::class, 'request']);
            Route::post('cancel', [MatchController::class, 'cancel']);
            Route::get('status', [MatchController::class, 'status']);
            Route::post('accept', [MatchController::class, 'accept']);
            Route::post('reject', [MatchController::class, 'reject']);
            Route::get('history', [MatchController::class, 'history']);
        });

        // Phase 1.6 - Chat
        Route::get('conversations', [ConversationController::class, 'index']);
        Route::post('conversations', [ConversationController::class, 'store']);
        Route::get('conversations/{id}', [ConversationController::class, 'show']);
        Route::delete('conversations/{id}', [ConversationController::class, 'destroy']);
        Route::get('conversations/{id}/messages', [MessageController::class, 'index']);
        Route::post('conversations/{id}/messages', [MessageController::class, 'store']);
        Route::post('messages/{id}/read', [MessageController::class, 'markRead']);
        Route::post('messages/typing', [MessageController::class, 'typing']);

        // Phase 1.7 - Audio Calling
        Route::prefix('calls/audio')->group(function () {
            Route::post('initiate', [AudioCallController::class, 'initiate']);
            Route::post('accept', [AudioCallController::class, 'accept']);
            Route::post('reject', [AudioCallController::class, 'reject']);
            Route::post('end', [AudioCallController::class, 'end']);
            Route::get('history', [AudioCallController::class, 'history']);
        });

        // Phase 1.8 - Video Calling
        Route::prefix('calls/video')->group(function () {
            Route::post('initiate', [VideoCallController::class, 'initiate']);
            Route::post('accept', [VideoCallController::class, 'accept']);
            Route::post('reject', [VideoCallController::class, 'reject']);
            Route::post('end', [VideoCallController::class, 'end']);
            Route::get('history', [VideoCallController::class, 'history']);
        });

        Route::post('agora/token', [AgoraController::class, 'token']);

        // Phase 1.9 - Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/read', [NotificationController::class, 'markRead']);
        Route::post('fcm/token', [NotificationController::class, 'updateFcmToken']);
    });
});
