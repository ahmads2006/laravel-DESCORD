<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'discord_id',
        'name',
        'color',
        'hoist',
        'position',
        'permissions',
        'managed',
        'mentionable',
    ];

    protected $casts = [
        'color' => 'integer',
        'hoist' => 'boolean',
        'position' => 'integer',
        'managed' => 'boolean',
        'mentionable' => 'boolean',
    ];
}
