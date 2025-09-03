<?php

return [
    'providers' => [
        'einvoice' => [
            'client_id'     => env('OAUTH_EINVOICE_CLIENT_ID'),
            'auth_url'      => env('OAUTH_EINVOICE_AUTH_URL'),
            'token_url'     => env('OAUTH_EINVOICE_TOKEN_URL'),
            'userinfo_url'  => env('OAUTH_EINVOICE_USERINFO_URL'),
            'redirect_uri'  => env('OAUTH_EINVOICE_REDIRECT_URI'),
            'scope'         => env('OAUTH_EINVOICE_SCOPE', 'user:read'),
        ],
        // Add other providers here (google, custom, etc.)
    ],
];