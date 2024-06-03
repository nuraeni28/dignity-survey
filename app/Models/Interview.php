<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $tables = 'interviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'interview_date',
        'photo',
        'location',
        'long',
        'lat',
        'interview_schedule_id',
        'respondent_id',
        'owner_id',
        'admin_id',
        'start_time',
        'end_time',
        'record_file',
    ];

    protected $casts = [
        'interview_schedule_id' => 'integer',
    ];

    /**
     * Get the user that owns the interview.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(InterviewSchedule::class);
    }

    /**
     * Get the customer that owns the interview.
     */
    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }

    /**
     * Get the interview_data for the interview.
     */
    public function data()
    {
        return $this->hasMany(InterviewData::class);
    }

    public function duration()
    {
        // dd($this->start_time, $this->end_time);
        if ($this->start_time && $this->end_time) {
            $start = new \DateTime($this->start_time);
            $end = new \DateTime($this->end_time);
            $diff = $start->diff($end);
            return $diff->format('%H:%I:%S');
        } else {
            return '00:00:00';
        }
    }
}
