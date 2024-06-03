<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caleg extends Model
{
    use HasFactory;
    protected $table = 'caleg';
    protected $fillable = ['name', 'partai_id'];
     protected $casts = [

        'partai_id' => 'integer'
    ];

       public function partai()
    {
        return $this->belongsTo(Partai::class, 'partai_id');
    }

}

