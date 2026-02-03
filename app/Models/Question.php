<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['specialization_id', 'language_id', 'question_text', 'is_active'];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
