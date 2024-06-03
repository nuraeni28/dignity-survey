<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $table = 'otp';

    protected $fillable = [
        'user_id',
        'otp_code',
        'number_phone',
        'expired_at',
        'tipe'
    ];

}
