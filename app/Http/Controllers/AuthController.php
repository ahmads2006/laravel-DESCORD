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

    public function callback()
    {
        try {
            $discordUser = Socialite::driver('discord')->stateless()->user();
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Discord OAuth failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect to frontend with error
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect($frontendUrl . '/login?error=discord_auth_failed&detail=' . urlencode($e->getMessage()));
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

        // Redirect to frontend with token
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return redirect($frontendUrl . '/auth/callback?token=' . $token);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
