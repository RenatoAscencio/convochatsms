<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ConvoChat API Configuration
    |--------------------------------------------------------------------------
    |
    | This is your ConvoChat API key which you can get from:
    | https://sms.convo.chat/dashboard/tools/keys
    |
    */

    'api_key' => env('CONVOCHAT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | ConvoChat Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for ConvoChat API endpoints. Usually you don't need to
    | change this unless ConvoChat provides a different endpoint.
    |
    */

    'base_url' => env('CONVOCHAT_BASE_URL', 'https://sms.convo.chat/api'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds for API calls to ConvoChat.
    |
    */

    'timeout' => (int) env('CONVOCHAT_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Log API Requests
    |--------------------------------------------------------------------------
    |
    | Set to true if you want to log all API requests and responses.
    | Useful for debugging but be careful in production.
    |
    */

    'log_requests' => env('CONVOCHAT_LOG_REQUESTS', false),

    /*
    |--------------------------------------------------------------------------
    | Default SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for SMS sending
    |
    */

    'sms' => [
        'default_mode' => env('CONVOCHAT_SMS_MODE', 'devices'), // 'devices' or 'credits'
        'default_priority' => env('CONVOCHAT_SMS_PRIORITY', 2), // 1 = high, 2 = normal
        'default_device' => env('CONVOCHAT_SMS_DEVICE', null),
        'default_gateway' => env('CONVOCHAT_SMS_GATEWAY', null),
        'default_sim' => env('CONVOCHAT_SMS_SIM', 1), // 1 or 2
    ],

    /*
    |--------------------------------------------------------------------------
    | Default WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for WhatsApp sending
    |
    */

    'whatsapp' => [
        'default_account' => env('CONVOCHAT_WA_ACCOUNT', ''),
        'default_priority' => env('CONVOCHAT_WA_PRIORITY', 2), // 1 = high, 2 = normal
    ],

];