<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DiscordOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DiscordAuthController extends Controller
{
    public function __construct(
        protected DiscordOAuthService $discordOAuth
    ) {}

    /**
     * Redirect user to Discord OAuth2 authorization page.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('discord_oauth_state', $state);

        $url = $this->discordOAuth->getAuthorizationUrl($state);

        return redirect()->away($url);
    }

    /**
     * Handle Discord OAuth2 callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->get('discord_oauth_state');
        if (! $state || $state !== $request->query('state')) {
            return redirect()->route('onboarding.login')
                ->with('error', 'Invalid state parameter. Please try again.');
        }

        $request->session()->forget('discord_oauth_state');

        $code = $request->query('code');
        if (! $code) {
            return redirect()->route('onboarding.login')
                ->with('error', 'Authorization was denied or no code was received.');
        }

        $tokenData = $this->discordOAuth->exchangeCodeForToken($code);
        if (! $tokenData) {
            return redirect()->route('onboarding.login')
                ->with('error', 'Failed to authenticate with Discord. Please try again.');
        }

        $accessToken = $tokenData['access_token'];
        $userData = $this->discordOAuth->fetchUser($accessToken);

        if (! $userData) {
            return redirect()->route('onboarding.login')
                ->with('error', 'Failed to fetch your Discord profile. Please try again.');
        }

        // Store access token in session for guild join / role assignment
        $request->session()->put('discord_access_token', $accessToken);

        $user = $this->findOrCreateUser($userData);
        Auth::login($user, remember: true);

        // Redirect to next onboarding step
        return $this->redirectToOnboardingStep($user);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('discord_access_token');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('onboarding.login')
            ->with('success', 'You have been logged out.');
    }

    protected function findOrCreateUser(array $userData): User
    {
        $discordId = (string) $userData['id'];
        $user = User::where('discord_id', $discordId)->first();

        if ($user) {
            $user->update([
                'name' => $userData['global_name'] ?? $userData['username'] ?? 'Unknown',
                'discord_username' => $userData['username'] ?? null,
                'discord_avatar' => $userData['avatar'] ?? null,
                'discord_email' => $userData['email'] ?? null,
            ]);

            return $user;
        }

        $email = $userData['email'] ?? $discordId . '@discord.local';
        $name = $userData['global_name'] ?? $userData['username'] ?? 'Unknown';

        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(64)),
            'discord_id' => $discordId,
            'discord_username' => $userData['username'] ?? null,
            'discord_avatar' => $userData['avatar'] ?? null,
            'discord_email' => $userData['email'] ?? null,
        ]);
    }

    protected function redirectToOnboardingStep(User $user): RedirectResponse
    {
        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('onboarding.success');
        }

        return redirect()->route('onboarding.language');
    }
}
