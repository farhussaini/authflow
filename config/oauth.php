<?php

return [
    'providers' => [
        'einvoice' => [
            'auth_url' => env('OAUTH_EINVOICE_AUTH_URL', 'https://auth.einvoiceme.com/oauth/authorize'),
            'token_url' => env('OAUTH_EINVOICE_TOKEN_URL', 'https://auth.einvoiceme.com/oauth/token'),
            'userinfo_url' => env('OAUTH_EINVOICE_USERINFO_URL', 'https://auth.einvoiceme.com/userinfo'),
            'client_id' => env('OAUTH_EINVOICE_CLIENT_ID'),
            'client_secret' => env('OAUTH_EINVOICE_CLIENT_SECRET'), 
            'redirect_uri' => env('OAUTH_EINVOICE_REDIRECT_URI'),
            'scope' => 'openid profile email',
       ],
       
    ],
];