<?php

namespace App\Services\Agora;

use App\Models\User;
use Illuminate\Support\Str;

class AgoraService
{
    public function generateChannelName(string $prefix, string $uuid): string
    {
        return "{$prefix}_{$uuid}";
    }

    public function generateUid(User $user): int
    {
        return abs(crc32($user->uuid)) % 2147483647 ?: 1;
    }

    public function buildToken(string $channelName, int $uid, string $role = 'publisher'): array
    {
        $appId = config('voiceconnect.agora.app_id');
        $certificate = config('voiceconnect.agora.app_certificate');
        $expiry = config('voiceconnect.agora.token_expiry_seconds', 3600);

        if (! $appId || ! $certificate) {
            return [
                'channel_name' => $channelName,
                'token' => 'dev_'.Str::random(32),
                'uid' => $uid,
                'expires_at' => now()->addSeconds($expiry)->toIso8601String(),
            ];
        }

        $token = $this->buildRtcToken($appId, $certificate, $channelName, $uid, $role, $expiry);

        return [
            'channel_name' => $channelName,
            'token' => $token,
            'uid' => $uid,
            'expires_at' => now()->addSeconds($expiry)->toIso8601String(),
        ];
    }

    private function buildRtcToken(
        string $appId,
        string $appCertificate,
        string $channelName,
        int $uid,
        string $role,
        int $expireSeconds,
    ): string {
        $privilegeExpiredTs = time() + $expireSeconds;
        $roleValue = $role === 'publisher' ? 1 : 2;

        $message = pack('V', $privilegeExpiredTs);
        $message .= pack('V', $roleValue);
        $message .= pack('V', $privilegeExpiredTs);

        $content = $appId.$channelName.(string) $uid.$message;
        $signature = hash_hmac('sha256', $content, $appCertificate, true);

        return base64_encode($signature.$message);
    }
}
