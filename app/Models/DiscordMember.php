<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'discord_id',
        'username',
        'global_name',
        'roles',
        'avatar',
        'joined_at',
    ];

    protected $casts = [
        'roles' => 'array',
        'joined_at' => 'datetime',
    ];
}
