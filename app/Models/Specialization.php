<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $fillable = ['slug', 'name', 'is_active', 'discord_role_id'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
