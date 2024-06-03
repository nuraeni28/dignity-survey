<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;
    
    protected $table = 'login_histories';
    protected $fillable = [
       'id_user'
    ];
    protected $hidden = [
        'created_at'
    ];
        public function user()
    {
        return $this->belongsTo(User::class);
    }

}