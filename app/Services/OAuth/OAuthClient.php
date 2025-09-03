<?php

namespace App\Services\OAuth;

use App\Models\OAuthToken;
use App\Services\OAuth\Contracts\OAuthClientContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Events\TokenRefreshed;

class OAuthClient implements OAuthClientContract
{
    protected $provider;
    protected $config;

    public function __construct(string $provider = 'einvoice')
    {
        $this->provider = $provider;
        $this->config = config("oauth.providers.{$provider}");
    }

    // Step 1: Generate redirect URL
    public function redirect(): string
    {
        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $codeVerifier = Str::random(64);
        session(['code_verifier' => $codeVerifier]);

        $codeChallenge = strtr(rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='), '+/', '-_');

        return $this->config['auth_url'] . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => $this->config['scope'],
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
    }

    // Step 2-3: Handle callback & exchange code for token
    public function handleCallback(array $queryParams): array
    {
        if ($queryParams['state'] !== session('oauth_state')) {
            abort(403, 'Invalid state');
        }

        $response = Http::asForm()->post($this->config['token_url'], [
            'grant_type' => 'authorization_code',
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'code_verifier' => session('code_verifier'),
            'code' => $queryParams['code'],
        ]);

        $data = $response->json();

        // Store token encrypted
        $token = OAuthToken::updateOrCreate(
            ['user_id' => auth()->id(), 'provider' => $this->provider],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? null,
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]
        );

        return $data;
    }

    // Step 5: Refresh token
    public function refreshToken(string $refreshToken): array
    {
        return Cache::lock("oauth_refresh_{$this->provider}", 10)->block(5, function () use ($refreshToken) {
            $response = Http::asForm()->post($this->config['token_url'], [
                'grant_type' => 'refresh_token',
                'client_id' => $this->config['client_id'],
                'refresh_token' => $refreshToken,
                'scope' => $this->config['scope'],
            ]);

            $data = $response->json();

            event(new TokenRefreshed(auth()->user(), $this->provider));

            return $data;
        });
    }

    public function getUserInfo(string $accessToken): array
    {
        return Http::withToken($accessToken)->get($this->config['userinfo_url'])->json();
    }
}