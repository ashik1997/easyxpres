<?php

return [
    'environment' => env('EPS_ENVIRONMENT', 'production'),

    'base_url' => env('EPS_BASE_URL', 'https://pgapi.eps.com.bd'),

    'credentials' => [
        'merchant_id' => env('EPS_MERCHANT_ID'),
        'store_id' => env('EPS_STORE_ID'),
        'username' => env('EPS_USERNAME'),
        'password' => env('EPS_PASSWORD'),
        'hash_key' => env('EPS_HASH_KEY'),
    ],

    'endpoints' => [
        'token' => '/v1/Auth/GetToken',
        'initialize' => '/v1/EPSEngine/InitializeEPS',
        'verify' => '/v1/EPSEngine/CheckMerchantTransactionStatus',
    ],

    'callbacks' => [
        'success' => env('EPS_SUCCESS_URL'),
        'fail' => env('EPS_FAIL_URL'),
        'cancel' => env('EPS_CANCEL_URL'),
        'ipn' => env('EPS_IPN_URL'),
    ],

    'ipn_encryption_key' => env('EPS_IPN_ENCRYPTION_KEY'),

    'currency' => 'BDT',
    'balance_currency' => 'MVR',
    'minimum_amount' => (float) env('EPS_MIN_AMOUNT', 10),
    'maximum_amount' => (float) env('EPS_MAX_AMOUNT', 500000),

    'connect_timeout' => (int) env('EPS_CONNECT_TIMEOUT', 5),
    'timeout' => (int) env('EPS_TIMEOUT', 15),
    'retry_times' => (int) env('EPS_RETRY_TIMES', 2),
    'retry_sleep_ms' => (int) env('EPS_RETRY_SLEEP_MS', 250),
    'token_cache_seconds' => (int) env('EPS_TOKEN_CACHE_SECONDS', 3300),

    'production_api_url' => 'https://pgapi.eps.com.bd',
    'production_checkout_hosts' => [
        'pg.eps.com.bd',
    ],

    // EPS requires customerEmail during initialization. A non-deliverable
    // address prevents the gateway from emailing payment receipts to users.
    'receipt_sink_email' => env('EPS_RECEIPT_SINK_EMAIL', 'no-reply@easyxpres.test'),

    'customer_defaults' => [
        'address' => env('EPS_CUSTOMER_ADDRESS', 'Dhaka, Bangladesh'),
        'city' => env('EPS_CUSTOMER_CITY', 'Dhaka'),
        'state' => env('EPS_CUSTOMER_STATE', 'Dhaka'),
        'postcode' => env('EPS_CUSTOMER_POSTCODE', '1000'),
        'country' => env('EPS_CUSTOMER_COUNTRY', 'BD'),
    ],
];
