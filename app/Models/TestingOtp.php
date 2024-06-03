<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestingOtp extends Model
{
    use HasFactory;
    protected $table = 'testing_otp';

    protected $fillable = [
        'nama',
        'user_id',
        'number_phone',
        'expired_at'
    ];
}
