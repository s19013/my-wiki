<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*','login','logout', 'sanctum/csrf-cookie','extended/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],
    //'allowed_origins' => ['http://34.168.7.200/'], // 本番環境用

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 元
    // 'supports_credentials' => false,

    'supports_credentials' => true,

    // allowed_originsで許可するホストを設定するのが望ましいらしいが
    // sanctumで認証を済まさないと通信できないように設定してあるから､大丈夫ではあるらしい｡

];
