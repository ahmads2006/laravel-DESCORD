<?php

namespace App\Services\Discord;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordBotService
{
    protected string $botToken;
    protected string $guildId;

    public function __construct()
    {
        $this->botToken = config('services.discord.bot_token');
        $this->guildId = config('services.discord.guild_id');
    }

    public function assignRole(string $discordUserId, string $roleId): bool
    {
        if (!$this->botToken || !$this->guildId) {
            Log::error('Discord Bot Token or Guild ID not configured.');
            return false;
        }

        $url = "https://discord.com/api/v10/guilds/{$this->guildId}/members/{$discordUserId}/roles/{$roleId}";

        $response = Http::withHeaders([
            'Authorization' => "Bot {$this->botToken}",
            'Content-Type' => 'application/json',
        ])->put($url);

        if ($response->successful()) {
            return true;
        }

        Log::error("Failed to assign Discord role: {$response->body()}", [
            'user_id' => $discordUserId,
            'role_id' => $roleId,
        ]);

        return false;
    }

    public function getMemberCount(): int
    {
        if (!$this->botToken || !$this->guildId) {
            return 0;
        }

        // with_counts=true is required to get approximate_member_count
        $url = "https://discord.com/api/v10/guilds/{$this->guildId}?with_counts=true";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bot {$this->botToken}",
            ])->get($url);

            if ($response->successful()) {
                return $response->json('approximate_member_count') ?? 0;
            }

            Log::error("Failed to fetch guild info: {$response->body()}");
        } catch (\Exception $e) {
            Log::error("Exception fetching guild info: {$e->getMessage()}");
        }

        return 0;
    }

    public function fetchGuildMembers(int $limit = 1000, string $after = '0'): array
    {
        if (!$this->botToken || !$this->guildId) {
            return [];
        }

        $allMembers = [];
        $url = "https://discord.com/api/v10/guilds/{$this->guildId}/members";

        do {
            $response = Http::withHeaders([
                'Authorization' => "Bot {$this->botToken}",
            ])->get($url, [
                'limit' => $limit,
                'after' => $after,
            ]);

            if (!$response->successful()) {
                Log::error("Failed to fetch members: {$response->body()}");
                break;
            }

            $members = $response->json();
            if (empty($members)) {
                break;
            }

            $allMembers = array_merge($allMembers, $members);
            
            // Prepare for next page
            $lastMember = end($members);
            $after = $lastMember['user']['id'];
            
            // Safety break if we got fewer than limit, meaning we reached the end
            if (count($members) < $limit) {
                break;
            }

        } while (true);

        return $allMembers;
    }
    public function fetchGuildRoles(): array
    {
        if (!$this->botToken || !$this->guildId) {
            return [];
        }

        $url = "https://discord.com/api/v10/guilds/{$this->guildId}/roles";

        $response = Http::withHeaders([
            'Authorization' => "Bot {$this->botToken}",
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("Failed to fetch roles: {$response->body()}");

        return [];
    }
}
