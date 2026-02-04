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
        return response()->json([
            'url' => Socialite::driver('discord')->scopes(['identify', 'email'])->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function callback()
    {
        try {
            $discordUser = Socialite::driver('discord')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to login with Discord.'], 401);
        }

        $user = User::updateOrCreate(
            ['discord_id' => $discordUser->id],
            [
                'discord_username' => $discordUser->name,
                'discord_avatar' => $discordUser->avatar,
                'discord_email' => $discordUser->email,
                'email' => $discordUser->email,
                'password' => bcrypt(str()->random(24)),
                'name' => $discordUser->name ?? 'Discord User',
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
