<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OAuthToken extends Model
{
    protected $fillable = [
        'user_id', 'provider', 'access_token', 'refresh_token', 'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = Crypt::encryptString($value);
    }

    public function getRefreshTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}