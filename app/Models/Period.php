<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'is_active',
        'user_id',
        'created_by'
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $dates = ['start_date', 'end_date'];
}