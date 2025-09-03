<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TokenRefreshed
{
    use SerializesModels;

    public $user;
    public $provider;

    public function __construct(User $user, string $provider)
    {
        $this->user = $user;
        $this->provider = $provider;
    }
}