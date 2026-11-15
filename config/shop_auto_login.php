<?php

return [
    'target_url' => env('SHOP_AUTO_LOGIN_URL', 'http://127.0.0.1:8002/auth/shop/auto-login'),
    'key' => env('SHOP_AUTO_LOGIN_KEY'),
    'ttl_seconds' => (int) env('SHOP_AUTO_LOGIN_TTL', 60),
    'clock_skew_seconds' => (int) env('SHOP_AUTO_LOGIN_CLOCK_SKEW', 5),
    'issuer' => env('SHOP_AUTO_LOGIN_ISSUER', 'easyxpress'),
    'audience' => env('SHOP_AUTO_LOGIN_AUDIENCE', 'ecom-shop'),
    'cache_store' => env('SHOP_AUTO_LOGIN_CACHE_STORE') ?: null,
];
