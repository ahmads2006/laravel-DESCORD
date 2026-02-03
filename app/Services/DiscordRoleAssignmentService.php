<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class DiscordRoleAssignmentService
{
    private const API_BASE = 'https://discord.com/api/v10';

    public function __construct(
        protected string $botToken,
        protected string $guildId,
        protected string $frontendRoleId,
        protected string $backendRoleId,
        protected string $solutionsArchitectRoleId,
    ) {}

    /**
     * Get the Discord role ID for the given specialization.
     */
    public function getRoleIdForSpecialization(string $specialization): ?string
    {
        return match ($specialization) {
            'frontend' => $this->frontendRoleId,
            'backend' => $this->backendRoleId,
            'solutions_architect' => $this->solutionsArchitectRoleId,
            default => null,
        };
    }

    /**
     * Add a user to the guild if they are not already a member.
     * Uses OAuth2 access token with guilds.join scope.
     */
    public function addUserToGuild(string $userId, string $accessToken): bool
    {
        $url = self::API_BASE . "/guilds/{$this->guildId}/members/{$userId}";

        $response = Http::withToken($this->botToken, 'Bot')
            ->put($url, [
                'access_token' => $accessToken,
            ]);

        // 201 = user was added, 204 = user already in guild
        if (in_array($response->status(), [200, 201, 204])) {
            return true;
        }

        Log::warning('Discord add user to guild failed', [
            'user_id' => $userId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }

    /**
     * Add a role to a guild member.
     * Requires bot to have MANAGE_ROLES permission (not Administrator).
     */
    public function addRoleToMember(string $userId, string $roleId): bool
    {
        $url = self::API_BASE . "/guilds/{$this->guildId}/members/{$userId}/roles/{$roleId}";

        $response = Http::withToken($this->botToken, 'Bot')
            ->put($url);

        if ($response->status() === 204) {
            return true;
        }

        Log::error('Discord add role to member failed', [
            'user_id' => $userId,
            'role_id' => $roleId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }

    /**
     * Check if a user is a member of the guild.
     */
    public function isMemberInGuild(string $userId): bool
    {
        $url = self::API_BASE . "/guilds/{$this->guildId}/members/{$userId}";

        $response = Http::withToken($this->botToken, 'Bot')
            ->get($url);

        return $response->successful();
    }

    /**
     * Assign the specialization role to a Discord user.
     * Adds user to guild first if needed (requires access token).
     */
    public function assignRole(string $discordUserId, string $specialization, ?string $accessToken = null): array
    {
        $roleId = $this->getRoleIdForSpecialization($specialization);

        if (! $roleId) {
            return [
                'success' => false,
                'message' => 'Invalid specialization.',
            ];
        }

        // Ensure user is in the guild (needed for new users via OAuth)
        if ($accessToken) {
            $this->addUserToGuild($discordUserId, $accessToken);
        }

        if (! $this->isMemberInGuild($discordUserId)) {
            return [
                'success' => false,
                'message' => 'You must be a member of the Discord server first. Please join using the invite link, then try again.',
            ];
        }

        if ($this->addRoleToMember($discordUserId, $roleId)) {
            return [
                'success' => true,
                'message' => 'Role assigned successfully!',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to assign role. The bot may not have permission to manage roles, or you may already have the role.',
        ];
    }
}
