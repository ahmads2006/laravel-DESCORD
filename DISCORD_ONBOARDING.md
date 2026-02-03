# Discord Onboarding System — Deployment Guide

This document explains how to deploy and configure the Discord onboarding web application.

## Overview

**Flow:** Login → Language Selection → Role Selection → Success

The system uses Discord OAuth2 for authentication and the Discord REST API for role assignment. No unofficial Discord libraries are used.

---

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm/pnpm
- MySQL or SQLite
- A Discord Application and Bot

---

## 1. Discord Developer Portal Setup

### Create an Application

1. Go to [Discord Developer Portal](https://discord.com/developers/applications)
2. Click **New Application** and name it
3. In **OAuth2 → General**:
   - Copy **Client ID** and **Client Secret**
   - Add redirect: `https://your-domain.com/auth/discord/callback` (must match your `APP_URL`)

### OAuth2 URL Generator

1. In **OAuth2 → URL Generator**
2. Scopes: `identify`, `email`, `guilds.join`
3. Copy the generated authorization URL (used internally by the app)

### Bot Setup

1. Go to **Bot** in the sidebar
2. Click **Add Bot**
3. Copy the **Token** (this is `DISCORD_BOT_TOKEN`)
4. **Important:** Do NOT enable "Administrator". Only grant:
   - `MANAGE_ROLES`
   - `CREATE_INSTANT_INVITE` (if you want to add users to the guild via OAuth)

### Invite Bot to Your Server

1. Go to **OAuth2 → URL Generator**
2. Scopes: `bot`
3. Permissions: `Manage Roles`, `Create Instant Invite` (optional)
4. Copy the URL and open it to add the bot to your server

---

## 2. Create Roles in Discord

Create these roles in your Discord server (Server Settings → Roles):

- **Frontend Developer** (or similar)
- **Backend Developer**
- **Solutions Architect**

Right-click each role → **Copy Role ID** (enable Developer Mode in Discord: User Settings → App Settings → Advanced → Developer Mode).

---

## 3. Environment Configuration

Copy `.env.example` to `.env` and fill in:

```env
APP_NAME="Discord Onboarding"
APP_URL=https://your-domain.com

# Database (SQLite for simple setup)
DB_CONNECTION=sqlite
# Or MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=discord_onboarding
# DB_USERNAME=root
# DB_PASSWORD=

# Discord OAuth2
DISCORD_CLIENT_ID=your_client_id
DISCORD_CLIENT_SECRET=your_client_secret
DISCORD_REDIRECT_URI="${APP_URL}/auth/discord/callback"

# Discord Bot
DISCORD_BOT_TOKEN=your_bot_token

# Discord Server & Roles
DISCORD_GUILD_ID=your_server_id
DISCORD_FRONTEND_ROLE_ID=role_id_for_frontend
DISCORD_BACKEND_ROLE_ID=role_id_for_backend
DISCORD_SOLUTIONS_ARCHITECT_ROLE_ID=role_id_for_solutions_architect
```

### Getting Guild ID

1. Enable Developer Mode in Discord
2. Right-click your server icon → **Copy Server ID**

---

## 4. Install & Run

```bash
# Install PHP dependencies
composer install

# Create .env and generate key (if not done)
cp .env.example .env
php artisan key:generate

# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Build frontend assets
npm install && npm run build

# Start the server
php artisan serve
```

For production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Security Checklist

- [ ] Bot has **MANAGE_ROLES** only (NOT Administrator)
- [ ] Bot role is **higher** than the roles it assigns (Discord requirement)
- [ ] `DISCORD_REDIRECT_URI` matches exactly what's in the Discord Developer Portal
- [ ] `.env` is not committed; secrets are in environment variables
- [ ] Use HTTPS in production (`APP_URL` must use `https://`)

---

## 6. Troubleshooting

### "Failed to assign role"
- Ensure the bot's role is **above** the target roles in the role list
- Verify the user is a member of the Discord server (they must join first, or use `guilds.join` scope to add them)
- Check `storage/logs/laravel.log` for API error details

### "Invalid state parameter"
- Session/cookies issue. Ensure `SESSION_DRIVER` is set (e.g. `database` or `file`) and sessions work
- If behind a load balancer, ensure sticky sessions or use Redis for sessions

### "Failed to authenticate with Discord"
- Verify `DISCORD_CLIENT_ID`, `DISCORD_CLIENT_SECRET`, and `DISCORD_REDIRECT_URI` are correct
- Redirect URI must match exactly (including trailing slash or lack thereof)

### User not in server
- Users must either join via an invite link first, OR the OAuth flow uses `guilds.join` to add them
- The app stores the OAuth access token temporarily to add users via the Add Guild Member endpoint

---

## 7. API Endpoints Used

- `GET https://discord.com/oauth2/authorize` — OAuth2 authorization
- `POST https://discord.com/api/oauth2/token` — Token exchange
- `GET https://discord.com/api/users/@me` — Fetch user info
- `PUT https://discord.com/api/v10/guilds/{guildId}/members/{userId}` — Add user to guild (OAuth token)
- `GET https://discord.com/api/v10/guilds/{guildId}/members/{userId}` — Check if user is in guild
- `PUT https://discord.com/api/v10/guilds/{guildId}/members/{userId}/roles/{roleId}` — Add role to member

---

## License

MIT
