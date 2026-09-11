<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CinetPay (Mobile Money + carte — agrégateur ivoirien)
    |--------------------------------------------------------------------------
    |
    | API v1 : https://api.cinetpay.net
    | - oauth/login : api_key + api_password (secret) → access_token
    | - POST /v1/payment : initialisation → payment_token + payment_url
    | - GET  /v1/payment/{merchant_transaction_id} : statut canonique
    |
    */

    'base_url' => env('CINETPAY_BASE_URL', 'https://api.cinetpay.net'),

    'api_key' => env('CINETPAY_API_KEY'),
    'site_id' => env('CINETPAY_SITE_ID'),
    'secret_key' => env('CINETPAY_SECRET_KEY'), // api_password de l'API v1

    'notify_url' => env('CINETPAY_NOTIFY_URL'),
    'return_url' => env('CINETPAY_RETURN_URL'),

    'currency' => 'XOF',
    'lang' => 'fr',
];
