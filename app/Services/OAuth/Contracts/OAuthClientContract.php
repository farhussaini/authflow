<?php

namespace App\Services\OAuth\Contracts;

interface OAuthClientContract
{
    public function redirect(): string;
    public function handleCallback(array $queryParams): array;
    public function refreshToken(string $refreshToken): array;
    public function getUserInfo(string $accessToken): array;
}