<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTargetInterviews extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'period_id',
        'target_interviews',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'period_id' => 'integer',
        'target_interviews' => 'integer',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interviews(){
        return $this->hasMany(InterviewSchedule::class, 'user_id', 'user_id')
            ->where('period_id', $this->period_id)
            ->has('interview')
            ->has('user')
            ->has('customer');
    }

    public function userTotalDoneInterviews(){
        return $this->interviews()->count();
    }
}
