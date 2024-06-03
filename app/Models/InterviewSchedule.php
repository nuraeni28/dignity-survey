<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewSchedule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_id',
        'user_id',
        'respondent_id',
        'interview_date',
        'target_interviews',
        'type'
    ];

    protected $casts = [
        'period_id' => 'integer',
        'user_id' => 'integer',
        'respondent_id' => 'integer',
        'interview_date' => 'datetime',
        'target_interviews' => 'integer',
        'type' => 'string',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }

    public function interview()
    {
        return $this->hasOne(Interview::class)->with('data', 'respondent', 'user');
    }
}