<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['test_id', 'question_id', 'choice_id', 'is_correct'];
}
