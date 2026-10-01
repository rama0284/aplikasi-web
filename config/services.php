<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'nutriscan_ai' => [
        'provider'       => env('NUTRISCAN_AI_PROVIDER', 'gemini'),
        'base_url'       => env('NUTRISCAN_AI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        // Untuk Gemini gunakan GEMINI_API_KEY, untuk 9Router gunakan NUTRISCAN_AI_API_KEY
        'api_key'        => env('NUTRISCAN_AI_PROVIDER', 'gemini') === '9router'
                            ? env('NUTRISCAN_AI_API_KEY')
                            : env('GEMINI_API_KEY', env('NUTRISCAN_AI_API_KEY')),
        'model'          => env('NUTRISCAN_AI_PROVIDER', 'gemini') === '9router'
                            ? env('NUTRISCAN_AI_9ROUTER_MODEL', 'gpt-4o-mini')
                            : env('NUTRISCAN_AI_MODEL', 'gemini-3.8-flash'),
        'demo_mode'      => env('NUTRISCAN_DEMO_MODE', true),
    ],

];
