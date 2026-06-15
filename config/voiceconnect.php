<?php

return [
    'name' => env('APP_NAME', 'VoiceConnect'),

    'otp' => [
        'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 5),
        'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
    ],

    'agora' => [
        'app_id' => env('AGORA_APP_ID'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE'),
        'token_expiry_seconds' => (int) env('AGORA_TOKEN_EXPIRY', 3600),
    ],

    'wallet' => [
        'signup_bonus_coins' => (int) env('SIGNUP_BONUS_COINS', 0),
    ],
];
