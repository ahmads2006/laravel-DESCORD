<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Discord\DiscordOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')->scopes(['identify', 'email'])->redirect();
    }

    public function callback(DiscordOAuthService $oauth)
    {
        try {
            $discordUser = $oauth->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login with Discord.');
        }

        $user = User::updateOrCreate(
            ['discord_id' => $discordUser->id],
            [
                'discord_username' => $discordUser->name,
                'discord_avatar' => $discordUser->avatar,
                'discord_email' => $discordUser->email,
                'email' => $discordUser->email,
                // We don't really have a password for them, so we can leave it null or set loose one if needed.
                // Or make password nullable in DB (it usually is for social login users if configured right, or use a dummy)
                // Default create_users_table makes it string, not nullable. 
                // I should probably set a dummy password.
                'password' => bcrypt(str()->random(24)),
                'name' => $discordUser->name ?? 'Discord User',
            ]
        );

        Auth::login($user);

        return redirect()->route('language.select');
    }
}
