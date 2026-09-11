<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Instagram Graph API (compte Business/Creator relié à une Page Facebook)
    |--------------------------------------------------------------------------
    |
    | Token longue durée (60 jours, à rafraîchir via refresh_access_token).
    | Vérifier la version courante de l'API Graph : Meta la fait évoluer.
    |
    */

    'graph_version' => env('IG_GRAPH_VERSION', 'v21.0'),
    'access_token' => env('IG_ACCESS_TOKEN'),
    'business_account_id' => env('IG_BUSINESS_ACCOUNT_ID'),
    'app_id' => env('IG_APP_ID'),
    'app_secret' => env('IG_APP_SECRET'),

    // Cache local du flux pour éviter de taper l'API à chaque visite.
    'cache_ttl' => 60 * 60 * 24, // 24 h
];
