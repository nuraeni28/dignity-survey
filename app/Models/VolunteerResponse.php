<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerResponse extends Model
{
    use HasFactory;

    protected $table = 'volunteer_response';

      protected $fillable = [
        'id_user',
    ];

}
