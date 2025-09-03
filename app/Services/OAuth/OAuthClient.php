<?php

namespace App\Services\OAuth;

use App\Models\OAuthToken;
use App\Services\OAuth\Contracts\OAuthClientContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OAuthClient implements OAuthClientContract
{
    protected string $provider;
    protected array $config;

    public function __construct(string $provider = 'einvoice')
    {
        $this->provider = $provider;
        $this->config = [
            'client_id'     => env('OAUTH_EINVOICE_CLIENT_ID'),
            'client_secret' => env('OAUTH_EINVOICE_CLIENT_SECRET'),
            'auth_url'      => env('OAUTH_EINVOICE_AUTH_URL'),
            'token_url'     => env('OAUTH_EINVOICE_TOKEN_URL'),
            'userinfo_url'  => env('OAUTH_EINVOICE_USERINFO_URL'),
            'redirect_uri'  => env('OAUTH_EINVOICE_REDIRECT_URI'),
            'scope'         => env('OAUTH_EINVOICE_SCOPE'), // must match exactly
        ];
    }

    /**
     * Step 1: Generate the redirect URL with PKCE
     */
    public function redirect(): string
    {
        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $codeVerifier = Str::random(64);
        session(['code_verifier' => $codeVerifier]);

        $codeChallenge = strtr(
            rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='),
            '+/',
            '-_'
        );

        $query = http_build_query([
            'response_type'          => 'code',
            'client_id'              => $this->config['client_id'],
            'redirect_uri'           => $this->config['redirect_uri'],
            'scope'                  => $this->config['scope'],
            'state'                  => $state,
            'code_challenge'         => $codeChallenge,
            'code_challenge_method'  => 'S256',
        ]);

        return $this->config['auth_url'] . '?' . $query;
    }

    /**
     * Step 2 & 3: Handle the callback & exchange code for token
     */
    public function handleCallback(array $queryParams): array
    {
        if (!isset($queryParams['code']) || !isset($queryParams['state'])) {
            \Log::error('OAuth callback missing code or state', $queryParams);
            abort(400, 'Authorization code or state missing.');
        }

        $storedState = session()->pull('oauth_state');
        if ($queryParams['state'] !== $storedState) {
            abort(403, 'Invalid state');
        }

        $codeVerifier = session()->pull('code_verifier');

        $response = Http::asForm()->post($this->config['token_url'], [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config['client_id'], // PKCE: no client_secret needed
            'client_secret' => $this->config['client_secret'], // Not PKCE, So client_secret needed
            'redirect_uri'  => $this->config['redirect_uri'],
            'code_verifier' => $codeVerifier,
            'code'          => $queryParams['code'],
        ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['access_token'])) {
            \Log::error('OAuth token exchange failed', [
                'status' => $response->status(),
                'body'   => $data
            ]);
            abort(500, 'OAuth token exchange failed. Check logs.');
        }

        // Step 4: Fetch user info and create or update local user
        try {
            $userInfo = $this->getUserInfo($data['access_token']);
            if (isset($userInfo['sub'])) {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $userInfo['email']],
                    [
                        'name' => $userInfo['name'], 
                        'email' => $userInfo['email'],
                        'password' => bcrypt(Str::random(16)), // Random password OR Remove from users migration
                    ]
                );
            } else {
                \Log::error('OAuth user info missing Email', $userInfo);
                abort(500, 'OAuth user info missing Email. Check logs.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch OAuth user info', ['error' => $e->getMessage()]);
            abort(500, 'Failed to fetch OAuth user info. Check logs.');
        }

        OAuthToken::updateOrCreate(
            ['user_id' => $user->id, 'provider' => $this->provider],
            [
                'access_token'  => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? null,
                'expires_at'    => now()->addSeconds($data['expires_in']),
            ]
        );

        return $user->toArray();
    }

    /**
     * Step 5: Refresh token
     */
    public function refreshToken(string $refreshToken): array
    {
        return Http::asForm()->post($this->config['token_url'], [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->config['client_id'],
            'refresh_token' => $refreshToken,
            'scope'         => $this->config['scope'],
        ])->json();
    }

    /**
     * Step 6: Get authenticated user info
     */
    public function getUserInfo(string $accessToken): array
    {
        $response = Http::withToken($accessToken)->get($this->config['userinfo_url']);
        return $response->json() ?? [];
    }
}