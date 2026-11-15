<?php

return [
    'key' => env('SHOP_WALLET_KEY') ?: (env('SHOP_AUTO_LOGIN_KEY')
        ? hash_hmac('sha256', 'easyxpress-wallet-v1', (string) env('SHOP_AUTO_LOGIN_KEY'))
        : null),
    'clock_skew_seconds' => (int) env('SHOP_WALLET_CLOCK_SKEW', 300),
];
