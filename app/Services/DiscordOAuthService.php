<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordOAuthService
{
    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected string $redirectUri,
    ) {}

    /**
     * Generate the Discord OAuth2 authorization URL.
     */
    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', config('discord.oauth.scopes', ['identify', 'email', 'guilds.join'])),
            'state' => $state,
        ]);

        return config('discord.oauth.authorize_url') . '?' . $params;
    }

    /**
     * Exchange authorization code for access token.
     *
     * @return array{access_token: string, token_type: string, expires_in: int, refresh_token?: string, scope: string}|null
     */
    public function exchangeCodeForToken(string $code): ?array
    {
        $response = Http::asForm()->post(config('discord.oauth.token_url'), [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        if (! $response->successful()) {
            Log::error('Discord OAuth token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Fetch Discord user info using access token.
     *
     * @return array{id: string, username: string, avatar: ?string, email?: string, ...}|null
     */
    public function fetchUser(string $accessToken): ?array
    {
        $response = Http::withToken($accessToken)
            ->get(config('discord.oauth.user_url'));

        if (! $response->successful()) {
            Log::error('Discord user fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }
}
