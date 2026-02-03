<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncDiscordMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:sync-members';
    public function handle(\App\Services\Discord\DiscordBotService $discordBot)
    {
        // 1. Sync Roles
        $this->info('Starting Discord roles sync...');
        $roles = $discordBot->fetchGuildRoles();
        $rolesCount = count($roles);

        if ($rolesCount > 0) {
            $this->info("Found {$rolesCount} roles. Syncing...");
            $bar = $this->output->createProgressBar($rolesCount);
            $bar->start();

            foreach ($roles as $role) {
                \App\Models\DiscordRole::updateOrCreate(
                    ['discord_id' => $role['id']],
                    [
                        'name' => $role['name'],
                        'color' => $role['color'],
                        'hoist' => $role['hoist'],
                        'position' => $role['position'],
                        'permissions' => (string) $role['permissions'],
                        'managed' => $role['managed'],
                        'mentionable' => $role['mentionable'],
                    ]
                );
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Discord roles synced successfully!');
        } else {
            $this->error('Failed to fetch roles.');
        }

        $this->newLine();

        // 2. Sync Members
        $this->info('Starting Discord members sync...');

        $members = $discordBot->fetchGuildMembers();
        $count = count($members);

        if ($count === 0) {
            $this->error('No members found or failed to fetch members.');
            return;
        }

        $this->info("Found {$count} members. Syncing to database...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($members as $memberData) {
            $user = $memberData['user'];
            
            \App\Models\DiscordMember::updateOrCreate(
                ['discord_id' => $user['id']],
                [
                    'username' => $user['username'],
                    'global_name' => $user['global_name'] ?? null,
                    'avatar' => $user['avatar'] ?? null,
                    'roles' => $memberData['roles'] ?? [],
                    'joined_at' => isset($memberData['joined_at']) ? \Carbon\Carbon::parse($memberData['joined_at']) : null,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Discord members and roles synced successfully!');
    }
}
