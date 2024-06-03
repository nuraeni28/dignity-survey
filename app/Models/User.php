<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'nik',
        'address',
        'email_verified_at',
        'indonesia_province_id',
        'indonesia_city_id',
        'indonesia_district_id',
        'indonesia_village_id',
        'admin_id',
        'owner_id',
        'profile_image',
        'tps',
        'gender',
        'recomended_by',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'admin_id',
        'owner_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user province.
     */
    public function province()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Province::class, 'indonesia_province_id');
    }

    /**
     * Get the user city.
     */
    public function city()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\City::class, 'indonesia_city_id');
    }

    /**
     * Get the user district.
     */
    public function district()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\District::class, 'indonesia_district_id');
    }

    /**
     * Get the user village.
     */
    public function village()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Village::class, 'indonesia_village_id');
    }

    public function interviews()
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function statusLogin()
    {
       return $this->hasOne(LoginHistory::class, 'id_user')->latest();

    }


    public function targetInterview()
    {
        return $this->hasOne(UserTargetInterviews::class)->latestOfMany();
    }

    public function doneInterviews()
    {
        return $this->hasMany(InterviewSchedule::class)
            ->has('interview')
            ->has('user')
            ->has('customer');
    }

    public function totalDoneInterviews()
    {
        return $this->doneInterviews()->count();
    }
       public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
