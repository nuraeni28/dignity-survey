<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTutorial extends Model
{
    use HasFactory;
     protected $table = 'questions_tutorial_video';
     protected $fillable = [
        'question',
        'type',
        'answer',
        'correct_answer'
    ];
}
