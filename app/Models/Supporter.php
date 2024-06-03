<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPSTORM_META\map;

class Supporter extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'nik',
        'indonesia_province_id',
        'indonesia_city_id',
        'indonesia_district_id',
        'indonesia_village_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'surveyor_id' => 'integer'
    ];

    /**
     * Get the user that owns the customer.
     */

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
   
  
    
}
