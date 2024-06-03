<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvidenceComitment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['photo', 'id_surveyor', 'id_customer', 'location', 'long', 'lat'];

    protected $casts = [
        'id_surveyor' => 'integer',
        'id_customer' => 'integer',
    ];
     public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }   public function user()
    {
        return $this->belongsTo(User::class, 'id_surveyor');
    }
}
