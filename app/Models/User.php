<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'discord_email', // keeping this as it was in previous migration
        'language_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the Discord avatar URL.
     */
    public function getDiscordAvatarUrlAttribute(): ?string
    {
        if (! $this->discord_avatar || ! $this->discord_id) {
            return null;
        }

        $extension = str_starts_with($this->discord_avatar, 'a_') ? 'gif' : 'png';

        return "https://cdn.discordapp.com/avatars/{$this->discord_id}/{$this->discord_avatar}.{$extension}?size=128";
    }

    /**
     * Check if the user has completed onboarding.
     */
    public function hasCompletedOnboarding(): bool
    {
        // New logic: Has selected language and passed at least one test? 
        // Or just selected language? 
        // The requirements flow: Login -> Language -> Specialization -> Test -> Result.
        // Maybe "Completed Onboarding" means they are in the discord with a role?
        return $this->language_id !== null;
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
