<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerResponseDetail extends Model
{
    use HasFactory;
      protected $table = 'volunteer_response_detail';
     protected $fillable = [
        'id_tutorial_response',
        'id_question',
        'answer',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionTutorial::class, 'id_question');
    }
    public function response()
    {
        return $this->belongsTo(VolunteerResponse::class, 'id_tutorial_response');
    }

}
