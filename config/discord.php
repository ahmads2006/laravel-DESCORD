<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord OAuth2 & Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Discord OAuth2 authentication and bot role assignment.
    | Ensure the bot has MANAGE_ROLES permission (NOT Administrator).
    |
    */

    'client_id' => env('DISCORD_CLIENT_ID'),
    'client_secret' => env('DISCORD_CLIENT_SECRET'),
    'redirect_uri' => env('DISCORD_REDIRECT_URI'),
    'bot_token' => env('DISCORD_BOT_TOKEN'),

    'api_base' => 'https://discord.com/api/v10',

    'oauth' => [
        'authorize_url' => 'https://discord.com/oauth2/authorize',
        'token_url' => 'https://discord.com/api/oauth2/token',
        'user_url' => 'https://discord.com/api/users/@me',
        'scopes' => ['identify', 'email', 'guilds.join'],
    ],

    'guild_id' => env('DISCORD_GUILD_ID'),
    'frontend_role_id' => env('DISCORD_FRONTEND_ROLE_ID'),
    'backend_role_id' => env('DISCORD_BACKEND_ROLE_ID'),
    'solutions_architect_role_id' => env('DISCORD_SOLUTIONS_ARCHITECT_ROLE_ID'),

];
