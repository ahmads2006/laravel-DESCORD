<?php

namespace App\Services\Discord;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class DiscordOAuthService
{
    public function user(): User
    {
        return Socialite::driver('discord')->user();
    }
}
