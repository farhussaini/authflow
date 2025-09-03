<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OAuth\OAuthClient;

class ProfileController extends Controller
{
    public function me()
    {
        $user = auth()->user();
        $provider = request()->query('provider', 'einvoice');

        $token = $user->tokens()->where('provider', $provider)->first();

        if ($token->expires_at->isPast()) {
            $oauth = new OAuthClient($provider);
            $data = $oauth->refreshToken($token->refresh_token);

            $token->update([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? $token->refresh_token,
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]);
        }

        $oauth = new OAuthClient($provider);
        $profile = $oauth->getUserInfo($token->access_token);

        return response()->json($profile);
    }
}