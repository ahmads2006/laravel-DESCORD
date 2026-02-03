<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'user_id',
        'specialization_id',
        'language_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'correct_count',
        'incorrect_count',
        'passed',
        'completed',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'passed' => 'boolean',
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'test_questions');
    }
}
