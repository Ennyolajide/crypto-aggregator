<?php

return [
    
    // Price update settings
    'update_interval' => intval(env('CRYPTO_UPDATE_INTERVAL', 5)),

    // API settings
    'api' => [
        'timeout' => intval(env('CRYPTO_API_TIMEOUT', 5)),
        'retries' => intval(env('CRYPTO_API_RETRIES', 3)),
        'retry_delay' => intval(env('CRYPTO_API_RETRY_DELAY', 100)),
    ],

    'api_key' => env('CRYPTO_API_KEY'),
    'api_endpoint' => env('CRYPTO_API_ENDPOINT'),
    
    'exchanges' => explode(',', env('CRYPTO_EXCHANGES')),
    'pairs' => explode(',', env('CRYPTO_PAIRS', 'BTCUSDC,BTCUSDT,BTCETH')),

    // Caching settings
    'cache_ttl' => intval(env('CRYPTO_CACHE_TTL', 5)),
];
