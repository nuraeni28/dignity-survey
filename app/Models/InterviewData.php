<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewData extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'interview_id',
        'respondent_answer',
        'question_id',
    ];

    protected $casts = [
        'interview_id' => 'integer',
        'question_id' => 'integer',
    ];

    /**
     * Get the interview that owns the interview_data.
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class)->with('schedule');
    }

    public function schedule()
    {
        return $this->belongsTo(InterviewSchedule::class, 'id', 'interview_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }
}
